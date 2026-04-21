<?php

namespace Tigon\DmsConnect\Admin;

/**
 * Pricing admin page.
 *
 * Provides two tools:
 *   1. Bulk Price Update — change prices for products matching a
 *      filter (manufacturer/model/inventory status) with preview
 *      and example rows before applying.
 *   2. Global Pricing Rules — persistent rules that set prices by
 *      manufacturer/model/inventory-status/year. Rules are stored
 *      via the WordPress options API so they survive plugin updates.
 */
final class Pricing_Page
{
    /** Option key for persistent pricing rules (survives plugin updates). */
    const RULES_OPTION = 'tigon_dms_pricing_rules';

    /** Batch size for bulk price update jobs. */
    const BATCH_SIZE = 25;

    /** Meta key marking a product whose price is managed by this plugin. */
    const SOURCE_META = '_tigon_price_source';

    private function __construct() {}

    /* ─── Menu registration ────────────────────────────────────── */

    public static function add_pricing_page()
    {
        add_submenu_page(
            'tigon-dms-connect',
            'DMS Pricing',
            'Pricing',
            'manage_options',
            'dms-pricing',
            [self::class, 'render_page']
        );
    }

    /* ─── Storage helpers ──────────────────────────────────────── */

    /** @return array<string,array> */
    public static function get_rules(): array
    {
        $rules = get_option(self::RULES_OPTION, []);
        return is_array($rules) ? $rules : [];
    }

    public static function save_rules(array $rules): void
    {
        update_option(self::RULES_OPTION, $rules, false);
    }

    private static function normalise_rule(array $input, ?array $existing = null): array
    {
        $now = time();
        return [
            'id'                  => $existing['id'] ?? ('rule_' . wp_generate_password(12, false)),
            'name'                => sanitize_text_field($input['name'] ?? ''),
            'manufacturer_id'     => (int) ($input['manufacturer_id'] ?? 0),
            'model_id'            => (int) ($input['model_id'] ?? 0),
            'inventory_status_id' => (int) ($input['inventory_status_id'] ?? 0),
            'year'                => (int) ($input['year'] ?? 0),
            'price'               => (float) ($input['price'] ?? 0),
            'sale_price'          => (float) ($input['sale_price'] ?? 0),
            'created_at'          => $existing['created_at'] ?? $now,
            'updated_at'          => $now,
            'last_applied_at'     => $existing['last_applied_at'] ?? 0,
            'last_applied_count'  => $existing['last_applied_count'] ?? 0,
        ];
    }

    /* ─── Product query ────────────────────────────────────────── */

    /**
     * Build a WP_Query args array filtering DMS products by taxonomy + year.
     * Any filter with a 0/empty value is treated as "any".
     */
    private static function build_query_args(int $manufacturer_id, int $model_id, int $inv_status_id, int $year): array
    {
        $tax_query = ['relation' => 'AND'];
        if ($manufacturer_id > 0) {
            $tax_query[] = ['taxonomy' => 'manufacturers', 'field' => 'term_id', 'terms' => $manufacturer_id];
        }
        if ($model_id > 0) {
            $tax_query[] = ['taxonomy' => 'models', 'field' => 'term_id', 'terms' => $model_id];
        }
        if ($inv_status_id > 0) {
            $tax_query[] = ['taxonomy' => 'inventory-status', 'field' => 'term_id', 'terms' => $inv_status_id];
        }

        $meta_query = [
            'relation' => 'AND',
            // Only DMS-synced products
            ['key' => '_dms_cart_id', 'compare' => 'EXISTS'],
        ];
        if ($year > 0) {
            $meta_query[] = ['key' => 'year', 'value' => (string) $year, 'compare' => '='];
        }

        $args = [
            'post_type'      => 'product',
            'post_status'    => ['publish', 'draft'],
            'posts_per_page' => -1,
            'fields'         => 'ids',
            'meta_query'     => $meta_query,
            'no_found_rows'  => true,
        ];
        if (count($tax_query) > 1) {
            $args['tax_query'] = $tax_query;
        }
        return $args;
    }

    private static function find_product_ids(int $manufacturer_id, int $model_id, int $inv_status_id, int $year): array
    {
        $q = new \WP_Query(self::build_query_args($manufacturer_id, $model_id, $inv_status_id, $year));
        return array_map('intval', $q->posts ?? []);
    }

    /* ─── Price update helper ──────────────────────────────────── */

    /**
     * Apply a regular/sale price to a single product and mark it as
     * managed by this plugin so subsequent DMS syncs skip the price.
     *
     * @param string $source Either 'bulk' (bulk update) or 'rule:<id>'
     */
    private static function apply_price_to_product(int $pid, float $price, float $sale_price = 0, string $source = 'bulk'): bool
    {
        if (!get_post($pid)) return false;
        $formatted = number_format($price, 2, '.', '');
        update_post_meta($pid, '_regular_price', $formatted);

        if ($sale_price > 0 && $sale_price < $price) {
            $sale_fmt = number_format($sale_price, 2, '.', '');
            update_post_meta($pid, '_sale_price', $sale_fmt);
            update_post_meta($pid, '_price', $sale_fmt);
        } else {
            delete_post_meta($pid, '_sale_price');
            update_post_meta($pid, '_price', $formatted);
        }

        // Mark the product so DMS sync skips it. Presence = locked.
        update_post_meta($pid, self::SOURCE_META, sanitize_text_field($source));

        if (function_exists('tigon_dms_refresh_wc_product_data')) {
            tigon_dms_refresh_wc_product_data($pid);
        } elseif (function_exists('wc_delete_product_transients')) {
            wc_delete_product_transients($pid);
        }
        return true;
    }

    /** Check whether a product's price is currently locked by this plugin. */
    public static function is_price_managed(int $pid): bool
    {
        return (bool) get_post_meta($pid, self::SOURCE_META, true);
    }

    /** Read the source string ('bulk', 'rule:<id>', or '' if unmanaged). */
    public static function get_price_source(int $pid): string
    {
        $v = get_post_meta($pid, self::SOURCE_META, true);
        return is_string($v) ? $v : '';
    }

    /* ─── Page rendering (stub — will be expanded) ─────────────── */

    public static function render_page()
    {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }
        self::render_ui();
    }

    /* ─── Taxonomy option helpers ──────────────────────────────── */

    private static function term_options(string $taxonomy): array
    {
        $terms = get_terms(['taxonomy' => $taxonomy, 'hide_empty' => false, 'orderby' => 'name']);
        if (is_wp_error($terms) || empty($terms)) return [];
        $out = [];
        foreach ($terms as $t) {
            $out[] = ['id' => (int) $t->term_id, 'name' => $t->name, 'count' => (int) $t->count];
        }
        return $out;
    }

    /** Distinct vehicle years present in DMS products. */
    private static function year_options(): array
    {
        global $wpdb;
        $rows = $wpdb->get_col(
            "SELECT DISTINCT pm.meta_value
             FROM {$wpdb->postmeta} pm
             INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             INNER JOIN {$wpdb->postmeta} dms ON p.ID = dms.post_id AND dms.meta_key = '_dms_cart_id'
             WHERE pm.meta_key = 'year' AND pm.meta_value != ''
               AND p.post_type = 'product' AND p.post_status IN ('publish','draft')
             ORDER BY pm.meta_value DESC"
        );
        $out = [];
        foreach ((array) $rows as $y) {
            $y = (int) $y;
            if ($y > 0) $out[] = $y;
        }
        return $out;
    }

    private static function render_select(string $id, array $options, string $any_label = 'Any'): string
    {
        $html  = '<select id="' . esc_attr($id) . '" style="padding:0.4rem 0.6rem; border:1px solid #8c8f94; border-radius:4px; min-width:220px;">';
        $html .= '<option value="0">' . esc_html($any_label) . '</option>';
        foreach ($options as $opt) {
            $label = $opt['name'] . (isset($opt['count']) ? ' (' . $opt['count'] . ')' : '');
            $html .= '<option value="' . esc_attr((string) $opt['id']) . '">' . esc_html($label) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    private static function render_year_select(string $id, array $years): string
    {
        $html  = '<select id="' . esc_attr($id) . '" style="padding:0.4rem 0.6rem; border:1px solid #8c8f94; border-radius:4px; min-width:140px;">';
        $html .= '<option value="0">Any</option>';
        foreach ($years as $y) {
            $html .= '<option value="' . esc_attr((string) $y) . '">' . esc_html((string) $y) . '</option>';
        }
        $html .= '</select>';
        return $html;
    }

    /* ─── Page UI ──────────────────────────────────────────────── */

    private static function render_ui()
    {
        $nonce = wp_create_nonce('tigon_dms_pricing_nonce');
        $mfrs  = self::term_options('manufacturers');
        $mdls  = self::term_options('models');
        $invs  = self::term_options('inventory-status');
        $years = self::year_options();

        Admin_Page::page_header();

        echo '<div class="body" style="display:flex; flex-direction:column;">';

        // ───── Section 1: Bulk Price Update ─────
        echo '<div class="action-box-group" style="grid-template-columns:1fr; grid-template-rows:auto;">';
        echo '<div class="action-box primary" style="flex-direction:column; gap:1rem; align-items:flex-start;">';
        echo '<h2 style="margin:0;">Bulk Price Update</h2>';
        echo '<p>Change the price of DMS-synced products that match the selected filters. Preview the count and sample products before applying. At least one filter is required.</p>';

        echo '<div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:flex-end; width:100%;">';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Manufacturer</label>' . self::render_select('dms-price-mfr', $mfrs) . '</div>';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Model</label>' . self::render_select('dms-price-model', $mdls) . '</div>';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Inventory Status</label>' . self::render_select('dms-price-inv', $invs) . '</div>';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Year</label>' . self::render_year_select('dms-price-year', $years) . '</div>';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Regular Price ($)</label><input type="number" id="dms-price-price" min="0" step="0.01" style="padding:0.4rem 0.6rem; border:1px solid #8c8f94; border-radius:4px; width:140px;" /></div>';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Sale Price <span style="font-weight:400; color:#666;">(optional)</span></label><input type="number" id="dms-price-sale" min="0" step="0.01" style="padding:0.4rem 0.6rem; border:1px solid #8c8f94; border-radius:4px; width:140px;" /></div>';
        echo '</div>';

        echo '<div style="display:flex; align-items:center; gap:0.75rem; margin-top:0.5rem;">';
        echo '<button type="button" id="dms-price-preview-btn" class="button" style="height:auto; padding:0.6rem 2rem; font-size:14px;">Preview Products</button>';
        echo '<button type="button" id="dms-price-apply-btn" class="button button-primary" style="height:auto; padding:0.6rem 2rem; font-size:14px; background-color:var(--accent-color); color:var(--font-light); display:none;">Apply Price Update</button>';
        echo '<button type="button" id="dms-bulk-release-btn" class="button" style="height:auto; padding:0.6rem 2rem; font-size:14px; margin-left:auto;" title="Unlock products previously updated by Bulk Price Update so the next DMS sync can restore their DMS price.">Release Bulk Locks</button>';
        echo '<span id="dms-price-spinner" class="spinner" style="float:none; margin-top:0;"></span>';
        echo '</div>';
        echo '<p style="margin:0; font-size:0.82rem; color:#555;"><strong>How locks work:</strong> Applying a price here locks those products so future DMS syncs won\'t overwrite them. Use <em>Release Bulk Locks</em> to let DMS prices take over again. Rule-based locks are unaffected by this button.</p>';
        echo '<div id="dms-price-results" style="display:none; width:100%;"></div>';
        echo '</div></div>';

        // ───── Section 2: Global Pricing Rules ─────
        echo '<div class="action-box-group" style="grid-template-columns:1fr; grid-template-rows:auto;">';
        echo '<div class="action-box primary" style="flex-direction:column; gap:1rem; align-items:flex-start; border-left:4px solid var(--accent-color);">';
        echo '<h2 style="margin:0; color:var(--accent-color);">Global Pricing Rules</h2>';
        echo '<p>Save reusable pricing rules by manufacturer, model, inventory status, and year. When DMS sync runs, matching products <strong>automatically</strong> receive their rule price — no manual refresh needed. The <strong>Refresh</strong> button re-applies a rule to all currently-matching products on demand. The <strong>Release</strong> button unlocks products so DMS prices take over again. Rules are stored in WordPress options and <strong>persist across plugin updates</strong>.</p>';

        echo '<div style="display:flex; flex-wrap:wrap; gap:1rem; align-items:flex-end; width:100%;">';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Rule Name <span style="font-weight:400; color:#666;">(optional)</span></label><input type="text" id="dms-rule-name" placeholder="e.g. Club Car Onward 2024 New" style="padding:0.4rem 0.6rem; border:1px solid #8c8f94; border-radius:4px; min-width:220px;" /></div>';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Manufacturer</label>' . self::render_select('dms-rule-mfr', $mfrs) . '</div>';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Model</label>' . self::render_select('dms-rule-model', $mdls) . '</div>';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Inventory Status</label>' . self::render_select('dms-rule-inv', $invs) . '</div>';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Year</label>' . self::render_year_select('dms-rule-year', $years) . '</div>';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Regular Price ($)</label><input type="number" id="dms-rule-price" min="0" step="0.01" style="padding:0.4rem 0.6rem; border:1px solid #8c8f94; border-radius:4px; width:140px;" /></div>';
        echo '<div style="display:flex; flex-direction:column; gap:0.25rem;"><label style="font-weight:600; font-size:0.85rem;">Sale Price <span style="font-weight:400; color:#666;">(optional)</span></label><input type="number" id="dms-rule-sale" min="0" step="0.01" style="padding:0.4rem 0.6rem; border:1px solid #8c8f94; border-radius:4px; width:140px;" /></div>';
        echo '</div>';

        echo '<div style="display:flex; align-items:center; gap:0.75rem;">';
        echo '<button type="button" id="dms-rule-save-btn" class="button button-primary" style="height:auto; padding:0.6rem 2rem; font-size:14px; background-color:var(--accent-color); color:var(--font-light);">Save Rule</button>';
        echo '<button type="button" id="dms-rule-cancel-btn" class="button" style="height:auto; padding:0.6rem 2rem; font-size:14px; display:none;">Cancel Edit</button>';
        echo '<input type="hidden" id="dms-rule-edit-id" value="" />';
        echo '<span id="dms-rule-spinner" class="spinner" style="float:none; margin-top:0;"></span>';
        echo '</div>';

        echo '<div id="dms-rule-results" style="width:100%; margin-top:0.5rem;"></div>';
        echo '<div id="dms-rules-list" style="width:100%;"><em>Loading rules…</em></div>';
        echo '</div></div>';

        echo '</div>'; // .body

        // Styles + Scripts
        self::render_styles_and_scripts($nonce);
    }

    private static function render_styles_and_scripts(string $nonce)
    {
        ?>
<style>
.dms-price-progress{display:none;width:100%;margin-top:0.5rem;}
.dms-price-bar-wrap{background:#e0e0e0;border-radius:6px;height:24px;overflow:hidden;position:relative;}
.dms-price-bar{height:100%;background:linear-gradient(90deg,var(--accent-color),var(--main-color));border-radius:6px;transition:width 0.3s ease;}
.dms-price-text{position:absolute;top:0;left:0;right:0;bottom:0;display:flex;align-items:center;justify-content:center;font-size:0.78rem;font-weight:700;color:#fff;text-shadow:0 1px 2px rgba(0,0,0,0.3);}
.dms-price-status{font-size:0.82rem;color:#555;margin-top:0.4rem;}
.dms-rules-table{width:100%;border-collapse:collapse;font-size:0.9rem;}
.dms-rules-table th, .dms-rules-table td{padding:0.6rem 0.75rem;border-bottom:1px solid #e5e5e5;text-align:left;vertical-align:middle;}
.dms-rules-table th{background:#f6f7f7;font-weight:600;font-size:0.82rem;text-transform:uppercase;color:#555;}
.dms-rules-table tr:hover{background:#fafafa;}
.dms-rule-actions{display:flex;gap:0.4rem;flex-wrap:nowrap;}
.dms-rule-actions button{padding:0.35rem 0.8rem;font-size:0.82rem;height:auto;min-width:auto;}
.dms-last-applied{font-size:0.78rem;color:#666;}
</style>
<script>
jQuery(document).ready(function($){
    var pricingNonce = <?php echo wp_json_encode($nonce); ?>;

    function fmtMoney(v){ v = parseFloat(v); return isNaN(v) ? '—' : ('$' + v.toFixed(2)); }
    function esc(s){ return $('<span>').text(s == null ? '' : s).html(); }

    /* ── Bulk Price Update ─────────────────────────────────── */
    var $bpPreview = $('#dms-price-preview-btn');
    var $bpApply = $('#dms-price-apply-btn');
    var $bpSpinner = $('#dms-price-spinner');
    var $bpResults = $('#dms-price-results');
    var bpSyncId = '';

    $bpPreview.on('click', function(){
        var data = {
            action: 'tigon_dms_pricing_bulk_preview',
            nonce: pricingNonce,
            manufacturer_id: $('#dms-price-mfr').val(),
            model_id: $('#dms-price-model').val(),
            inventory_status_id: $('#dms-price-inv').val(),
            year: $('#dms-price-year').val(),
            price: $('#dms-price-price').val(),
            sale_price: $('#dms-price-sale').val()
        };

        $bpPreview.prop('disabled', true).text('Scanning...');
        $bpApply.hide();
        $bpSpinner.addClass('is-active');
        $bpResults.hide();

        $.ajax({ url: ajaxurl, type:'POST', data: data, timeout: 120000 }).done(function(resp){
            $bpSpinner.removeClass('is-active');
            $bpPreview.prop('disabled', false).text('Preview Products');
            if (!resp.success) { showErr($bpResults, resp.data || 'Preview failed'); return; }
            var d = resp.data;
            bpSyncId = d.sync_id;

            var html = '<div style="background:#fff3cd;border:1px solid #ffc107;padding:1rem;border-radius:6px;margin-top:0.5rem;">';
            html += '<h3 style="margin:0 0 0.5rem 0;color:#856404;">Preview: ' + d.total + ' matching products</h3>';
            html += '<ul style="list-style:disc;padding-left:1.5rem;">';
            html += '<li><strong>Products that will be updated:</strong> ' + d.total + '</li>';
            html += '<li><strong>New regular price:</strong> ' + fmtMoney(d.price) + '</li>';
            if (d.sale_price) html += '<li><strong>New sale price:</strong> ' + fmtMoney(d.sale_price) + '</li>';
            html += '</ul>';

            if (d.examples && d.examples.length) {
                html += '<details open style="margin-top:0.75rem;"><summary style="cursor:pointer;font-weight:600;color:#856404;">Example products (' + d.examples.length + ' of ' + d.total + ')</summary>';
                html += '<table class="dms-rules-table" style="margin-top:0.5rem;background:#fff;">';
                html += '<thead><tr><th>Product</th><th>Slug</th><th>Current</th><th>New</th></tr></thead><tbody>';
                d.examples.forEach(function(ex){
                    html += '<tr><td>' + esc(ex.title) + ' <span style="color:#888;">(ID:' + ex.id + ')</span></td><td><code>' + esc(ex.slug) + '</code></td><td>' + fmtMoney(ex.current) + '</td><td><strong>' + fmtMoney(ex.new) + '</strong></td></tr>';
                });
                html += '</tbody></table></details>';
            }
            html += '</div>';
            $bpResults.html(html).show();
            if (d.total > 0) $bpApply.show().text('Apply to ' + d.total + ' Products');
        }).fail(function(xhr, status, err){
            $bpSpinner.removeClass('is-active');
            $bpPreview.prop('disabled', false).text('Preview Products');
            showErr($bpResults, 'Preview failed: ' + (err || status));
        });
    });

    $bpApply.on('click', function(){
        var total = parseInt($bpApply.text().replace(/\D/g, ''), 10) || 0;
        if (!confirm('Apply new price to ' + total + ' products? This cannot be undone.')) return;

        $bpApply.prop('disabled', true).text('Applying...');
        $bpPreview.prop('disabled', true);
        $bpSpinner.addClass('is-active');

        showProgress($bpResults, total, 'Applying price update...');
        var cumulative = { updated: 0, errors: 0 };

        function runBatch(){
            $.ajax({ url: ajaxurl, type:'POST', timeout: 280000,
                data: { action: 'tigon_dms_pricing_bulk_apply', nonce: pricingNonce, sync_id: bpSyncId }
            }).done(function(resp){
                if (!resp.success) { cumulative.errors++; finishBulk(cumulative, total); return; }
                var d = resp.data;
                cumulative.updated += (d.updated || 0);
                cumulative.errors += (d.errors || 0);
                var processed = d.processed || 0;
                var pct = total > 0 ? Math.min(Math.round(processed / total * 100), 100) : 0;
                $bpResults.find('.dms-price-bar').css('width', pct + '%');
                $bpResults.find('.dms-price-text').text(processed + ' / ' + total + ' (' + pct + '%)');
                $bpResults.find('.dms-price-status').text('Updating... ' + processed + ' of ' + total);
                if (d.done) { finishBulk(cumulative, total); } else { runBatch(); }
            }).fail(function(xhr, status, err){
                cumulative.errors++;
                finishBulk(cumulative, total, 'Network error: ' + (err || status));
            });
        }
        runBatch();
    });

    function finishBulk(stats, total, errMsg){
        $bpSpinner.removeClass('is-active');
        $bpApply.hide();
        $bpPreview.prop('disabled', false).text('Preview Products');
        var html = '<div style="background:#d4edda;border:1px solid #c3e6cb;padding:1rem;border-radius:6px;margin-top:0.5rem;">';
        html += '<h3 style="margin:0;color:#155724;">Bulk Price Update Complete</h3>';
        html += '<ul style="list-style:disc;padding-left:1.5rem;margin-top:0.5rem;">';
        html += '<li><strong>Matched:</strong> ' + total + '</li>';
        html += '<li><strong>Updated:</strong> ' + stats.updated + '</li>';
        if (stats.errors) html += '<li style="color:#dc3545;"><strong>Errors:</strong> ' + stats.errors + '</li>';
        if (errMsg) html += '<li style="color:#dc3545;">' + esc(errMsg) + '</li>';
        html += '</ul></div>';
        $bpResults.html(html).show();
    }

    function showErr($el, msg){ $el.html('<div style="background:#f8d7da;border:1px solid #f5c6cb;padding:1rem;border-radius:6px;margin-top:0.5rem;color:#721c24;">' + esc(msg) + '</div>').show(); }
    function showProgress($el, total, label){
        $el.html(
            '<div class="dms-price-progress" style="display:block;">' +
            '<div class="dms-price-bar-wrap"><div class="dms-price-bar" style="width:0%;"></div><div class="dms-price-text">Starting…</div></div>' +
            '<div class="dms-price-status">' + esc(label) + '</div></div>'
        ).show();
    }

    /* ── Pricing Rules ─────────────────────────────────────── */
    var $ruleSave = $('#dms-rule-save-btn');
    var $ruleCancel = $('#dms-rule-cancel-btn');
    var $ruleSpinner = $('#dms-rule-spinner');
    var $ruleResults = $('#dms-rule-results');
    var $rulesList = $('#dms-rules-list');
    var rulesCache = [];

    function loadRules(){
        $rulesList.html('<em>Loading rules…</em>');
        $.ajax({ url: ajaxurl, type:'POST', data:{ action:'tigon_dms_pricing_rules_list', nonce: pricingNonce }, timeout: 60000 })
        .done(function(resp){
            if (!resp.success) { $rulesList.html('<em>Failed to load rules.</em>'); return; }
            rulesCache = resp.data.rules || [];
            if (!rulesCache.length) { $rulesList.html('<div style="padding:1rem;background:#f6f7f7;border-radius:6px;color:#555;">No pricing rules yet. Fill the form above and click <strong>Save Rule</strong>.</div>'); return; }
            var html = '<table class="dms-rules-table" style="margin-top:0.5rem;">';
            html += '<thead><tr><th>Name</th><th>Manufacturer</th><th>Model</th><th>Inv. Status</th><th>Year</th><th>Regular</th><th>Sale</th><th>Last Applied</th><th style="width:260px;">Actions</th></tr></thead><tbody>';
            rulesCache.forEach(function(r){
                var lastApplied = r.last_applied_at ? (new Date(r.last_applied_at * 1000)).toLocaleString() + ' (' + (r.last_applied_count || 0) + ')' : '—';
                html += '<tr data-id="' + esc(r.id) + '">';
                html += '<td><strong>' + esc(r.name || '(unnamed)') + '</strong></td>';
                html += '<td>' + esc(r.manufacturer_name) + '</td>';
                html += '<td>' + esc(r.model_name) + '</td>';
                html += '<td>' + esc(r.inventory_status_name) + '</td>';
                html += '<td>' + esc(r.year_label) + '</td>';
                html += '<td>' + fmtMoney(r.price) + '</td>';
                html += '<td>' + (parseFloat(r.sale_price) > 0 ? fmtMoney(r.sale_price) : '—') + '</td>';
                html += '<td class="dms-last-applied">' + esc(lastApplied) + '</td>';
                html += '<td class="dms-rule-actions">';
                html += '<button type="button" class="button button-primary dms-rule-refresh" style="background-color:var(--accent-color);color:var(--font-light);" title="Re-apply this rule price to all matching products and lock them.">Refresh</button>';
                html += '<button type="button" class="button dms-rule-release" title="Unlock the products managed by this rule so the next DMS sync restores their DMS price.">Release</button>';
                html += '<button type="button" class="button dms-rule-edit">Edit</button>';
                html += '<button type="button" class="button dms-rule-delete" style="color:#dc3545;">Delete</button>';
                html += '</td></tr>';
            });
            html += '</tbody></table>';
            $rulesList.html(html);
        });
    }

    function resetRuleForm(){
        $('#dms-rule-edit-id').val('');
        $('#dms-rule-name').val('');
        $('#dms-rule-mfr, #dms-rule-model, #dms-rule-inv, #dms-rule-year').val('0');
        $('#dms-rule-price, #dms-rule-sale').val('');
        $ruleSave.text('Save Rule');
        $ruleCancel.hide();
    }

    $ruleCancel.on('click', resetRuleForm);

    $ruleSave.on('click', function(){
        var data = {
            action: 'tigon_dms_pricing_rule_save',
            nonce: pricingNonce,
            id: $('#dms-rule-edit-id').val(),
            name: $('#dms-rule-name').val(),
            manufacturer_id: $('#dms-rule-mfr').val(),
            model_id: $('#dms-rule-model').val(),
            inventory_status_id: $('#dms-rule-inv').val(),
            year: $('#dms-rule-year').val(),
            price: $('#dms-rule-price').val(),
            sale_price: $('#dms-rule-sale').val()
        };
        $ruleSave.prop('disabled', true).text('Saving...');
        $ruleSpinner.addClass('is-active');
        $ruleResults.empty();
        $.ajax({ url: ajaxurl, type:'POST', data: data, timeout: 60000 }).done(function(resp){
            $ruleSpinner.removeClass('is-active');
            $ruleSave.prop('disabled', false);
            if (!resp.success) { showErr($ruleResults, resp.data || 'Save failed'); $ruleSave.text('Save Rule'); return; }
            resetRuleForm();
            loadRules();
        }).fail(function(xhr, status, err){
            $ruleSpinner.removeClass('is-active');
            $ruleSave.prop('disabled', false).text('Save Rule');
            showErr($ruleResults, 'Save failed: ' + (err || status));
        });
    });

    $rulesList.on('click', '.dms-rule-refresh', function(){
        var $row = $(this).closest('tr');
        var id = $row.data('id');
        var $btn = $(this);
        $btn.prop('disabled', true).text('Refreshing...');
        $.ajax({ url: ajaxurl, type:'POST', data:{ action:'tigon_dms_pricing_rule_apply', nonce: pricingNonce, id: id }, timeout: 280000 })
        .done(function(resp){
            $btn.prop('disabled', false).text('Refresh');
            if (!resp.success) { showErr($ruleResults, resp.data || 'Refresh failed'); return; }
            var d = resp.data;
            $ruleResults.html('<div style="background:#d4edda;border:1px solid #c3e6cb;padding:0.75rem 1rem;border-radius:6px;color:#155724;">Rule refreshed: <strong>' + d.updated + '</strong> of ' + d.matched + ' matching products updated' + (d.errors ? ' (' + d.errors + ' errors)' : '') + '.</div>');
            loadRules();
        }).fail(function(xhr, status, err){
            $btn.prop('disabled', false).text('Refresh');
            showErr($ruleResults, 'Refresh failed: ' + (err || status));
        });
    });

    $rulesList.on('click', '.dms-rule-edit', function(){
        var id = $(this).closest('tr').data('id');
        var r = rulesCache.filter(function(x){ return x.id === id; })[0];
        if (!r) return;
        $('#dms-rule-edit-id').val(r.id);
        $('#dms-rule-name').val(r.name || '');
        $('#dms-rule-mfr').val(String(r.manufacturer_id || 0));
        $('#dms-rule-model').val(String(r.model_id || 0));
        $('#dms-rule-inv').val(String(r.inventory_status_id || 0));
        $('#dms-rule-year').val(String(r.year || 0));
        $('#dms-rule-price').val(r.price || '');
        $('#dms-rule-sale').val(r.sale_price > 0 ? r.sale_price : '');
        $ruleSave.text('Update Rule');
        $ruleCancel.show();
        $('html, body').animate({ scrollTop: $('#dms-rule-name').offset().top - 120 }, 300);
    });

    $rulesList.on('click', '.dms-rule-delete', function(){
        var id = $(this).closest('tr').data('id');
        if (!confirm('Delete this pricing rule? This will not change any existing product prices.')) return;
        $.ajax({ url: ajaxurl, type:'POST', data:{ action:'tigon_dms_pricing_rule_delete', nonce: pricingNonce, id: id }, timeout: 60000 })
        .done(function(resp){
            if (!resp.success) { showErr($ruleResults, resp.data || 'Delete failed'); return; }
            loadRules();
        });
    });

    $rulesList.on('click', '.dms-rule-release', function(){
        var id = $(this).closest('tr').data('id');
        var $btn = $(this);
        if (!confirm('Release this rule\'s lock on all products it manages?\n\nAfter release, the next DMS sync will restore each product\'s DMS price. This does NOT delete the rule — the rule itself remains and can be re-applied by clicking Refresh.')) return;
        $btn.prop('disabled', true).text('Releasing...');
        $.ajax({ url: ajaxurl, type:'POST', data:{ action:'tigon_dms_pricing_rule_release', nonce: pricingNonce, id: id }, timeout: 120000 })
        .done(function(resp){
            $btn.prop('disabled', false).text('Release');
            if (!resp.success) { showErr($ruleResults, resp.data || 'Release failed'); return; }
            $ruleResults.html('<div style="background:#e2e3e5;border:1px solid #d6d8db;padding:0.75rem 1rem;border-radius:6px;color:#383d41;">Released lock on <strong>' + resp.data.released + '</strong> product(s). Next DMS sync will restore DMS prices.</div>');
            loadRules();
        }).fail(function(xhr, status, err){
            $btn.prop('disabled', false).text('Release');
            showErr($ruleResults, 'Release failed: ' + (err || status));
        });
    });

    /* Bulk release (only bulk-update locks, not rule locks) */
    $('#dms-bulk-release-btn').on('click', function(){
        if (!confirm('Release the lock on all products updated via Bulk Price Update?\n\nThis does NOT affect products managed by a Global Pricing Rule. After release, the next DMS sync will restore those products\' DMS prices.')) return;
        var $btn = $(this);
        $btn.prop('disabled', true).text('Releasing...');
        $.ajax({ url: ajaxurl, type:'POST', data:{ action:'tigon_dms_pricing_bulk_release', nonce: pricingNonce }, timeout: 120000 })
        .done(function(resp){
            $btn.prop('disabled', false).text('Release Bulk Locks');
            if (!resp.success) { showErr($bpResults, resp.data || 'Release failed'); return; }
            $bpResults.html('<div style="background:#e2e3e5;border:1px solid #d6d8db;padding:0.75rem 1rem;border-radius:6px;color:#383d41;">Released lock on <strong>' + resp.data.released + '</strong> product(s). Next DMS sync will restore DMS prices.</div>').show();
        }).fail(function(xhr, status, err){
            $btn.prop('disabled', false).text('Release Bulk Locks');
            showErr($bpResults, 'Release failed: ' + (err || status));
        });
    });

    loadRules();
});
</script>
        <?php
    }

    public static function ajax_bulk_preview()
    {
        check_ajax_referer('tigon_dms_pricing_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized', 403);

        $manufacturer = (int) ($_POST['manufacturer_id'] ?? 0);
        $model        = (int) ($_POST['model_id'] ?? 0);
        $inv_status   = (int) ($_POST['inventory_status_id'] ?? 0);
        $year         = (int) ($_POST['year'] ?? 0);
        $new_price    = (float) ($_POST['price'] ?? 0);
        $sale_price   = (float) ($_POST['sale_price'] ?? 0);

        if ($manufacturer === 0 && $model === 0 && $inv_status === 0 && $year === 0) {
            wp_send_json_error('Please select at least one filter (manufacturer, model, inventory status, or year).');
        }
        if ($new_price <= 0) {
            wp_send_json_error('Please enter a valid price greater than 0.');
        }

        $ids = self::find_product_ids($manufacturer, $model, $inv_status, $year);
        $total = count($ids);

        // Build example rows (first 15 products showing current vs new price)
        $examples = [];
        foreach (array_slice($ids, 0, 15) as $pid) {
            $title   = get_the_title($pid);
            $current = (float) get_post_meta($pid, '_regular_price', true);
            $examples[] = [
                'id'      => $pid,
                'title'   => $title ?: ('ID:' . $pid),
                'current' => $current > 0 ? number_format($current, 2, '.', '') : '—',
                'new'     => number_format($new_price, 2, '.', ''),
                'slug'    => get_post_field('post_name', $pid),
            ];
        }

        // Store plan in transient so the apply step doesn't re-query
        $sync_id = 'dms_price_' . wp_generate_password(16, false);
        set_transient($sync_id, [
            'ids'        => $ids,
            'offset'     => 0,
            'price'      => $new_price,
            'sale_price' => $sale_price,
        ], HOUR_IN_SECONDS);

        wp_send_json_success([
            'sync_id'    => $sync_id,
            'total'      => $total,
            'examples'   => $examples,
            'batch_size' => self::BATCH_SIZE,
            'price'      => number_format($new_price, 2, '.', ''),
            'sale_price' => $sale_price > 0 ? number_format($sale_price, 2, '.', '') : '',
        ]);
    }

    public static function ajax_bulk_apply()
    {
        check_ajax_referer('tigon_dms_pricing_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized', 403);

        ignore_user_abort(true);
        set_time_limit(270);

        $sync_id = sanitize_text_field($_POST['sync_id'] ?? '');
        $meta    = get_transient($sync_id);
        if (!is_array($meta) || !isset($meta['ids'])) {
            wp_send_json_error('Session expired. Please preview again.');
        }

        $ids        = $meta['ids'];
        $offset     = (int) ($meta['offset'] ?? 0);
        $price      = (float) ($meta['price'] ?? 0);
        $sale_price = (float) ($meta['sale_price'] ?? 0);
        $batch      = array_slice($ids, $offset, self::BATCH_SIZE);

        $updated = 0;
        $errors  = 0;
        foreach ($batch as $pid) {
            try {
                if (self::apply_price_to_product((int) $pid, $price, $sale_price, 'bulk')) {
                    $updated++;
                } else {
                    $errors++;
                }
            } catch (\Throwable $e) {
                $errors++;
            }
        }

        $new_offset = $offset + count($batch);
        $done       = $new_offset >= count($ids);

        if ($done) {
            delete_transient($sync_id);
            if (function_exists('wc_update_product_lookup_tables')) {
                wc_update_product_lookup_tables();
            }
        } else {
            $meta['offset'] = $new_offset;
            set_transient($sync_id, $meta, HOUR_IN_SECONDS);
        }

        wp_send_json_success([
            'updated'   => $updated,
            'errors'    => $errors,
            'processed' => $new_offset,
            'total'     => count($ids),
            'done'      => $done,
        ]);
    }

    /* ─── AJAX: Pricing Rules (CRUD + refresh) ─────────────────── */

    public static function ajax_rules_list()
    {
        check_ajax_referer('tigon_dms_pricing_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized', 403);

        $rules = self::get_rules();
        // Enrich with term labels for display
        $out = [];
        foreach ($rules as $rule) {
            $out[] = self::decorate_rule($rule);
        }
        wp_send_json_success(['rules' => $out]);
    }

    private static function decorate_rule(array $rule): array
    {
        $mfr_name = $rule['manufacturer_id'] ? (get_term($rule['manufacturer_id'])->name ?? '') : '';
        $mdl_name = $rule['model_id'] ? (get_term($rule['model_id'])->name ?? '') : '';
        $inv_name = $rule['inventory_status_id'] ? (get_term($rule['inventory_status_id'])->name ?? '') : '';
        $rule['manufacturer_name']    = $mfr_name ?: 'Any';
        $rule['model_name']           = $mdl_name ?: 'Any';
        $rule['inventory_status_name']= $inv_name ?: 'Any';
        $rule['year_label']           = $rule['year'] > 0 ? (string) $rule['year'] : 'Any';
        return $rule;
    }

    public static function ajax_rule_save()
    {
        check_ajax_referer('tigon_dms_pricing_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized', 403);

        $input = [
            'name'                => $_POST['name'] ?? '',
            'manufacturer_id'     => $_POST['manufacturer_id'] ?? 0,
            'model_id'            => $_POST['model_id'] ?? 0,
            'inventory_status_id' => $_POST['inventory_status_id'] ?? 0,
            'year'                => $_POST['year'] ?? 0,
            'price'               => $_POST['price'] ?? 0,
            'sale_price'          => $_POST['sale_price'] ?? 0,
        ];

        if ((float) $input['price'] <= 0) {
            wp_send_json_error('Price must be greater than 0.');
        }
        if ((int) $input['manufacturer_id'] === 0
            && (int) $input['model_id'] === 0
            && (int) $input['inventory_status_id'] === 0
            && (int) $input['year'] === 0) {
            wp_send_json_error('At least one filter (manufacturer, model, inventory status, or year) is required.');
        }

        $rules   = self::get_rules();
        $rule_id = sanitize_text_field($_POST['id'] ?? '');

        if ($rule_id && isset($rules[$rule_id])) {
            $rules[$rule_id] = self::normalise_rule($input, $rules[$rule_id]);
            $saved_id = $rule_id;
        } else {
            $new_rule = self::normalise_rule($input);
            $saved_id = $new_rule['id'];
            $rules[$saved_id] = $new_rule;
        }

        self::save_rules($rules);
        wp_send_json_success(['id' => $saved_id, 'rule' => self::decorate_rule($rules[$saved_id])]);
    }

    public static function ajax_rule_delete()
    {
        check_ajax_referer('tigon_dms_pricing_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized', 403);

        $rule_id = sanitize_text_field($_POST['id'] ?? '');
        $rules   = self::get_rules();
        if (!$rule_id || !isset($rules[$rule_id])) {
            wp_send_json_error('Rule not found.');
        }
        unset($rules[$rule_id]);
        self::save_rules($rules);
        wp_send_json_success(['id' => $rule_id]);
    }

    /**
     * Refresh a single rule — re-applies its price to every matching
     * product. Runs synchronously (one rule at a time should be fine
     * for reasonable inventory sizes). If the rule matches more than
     * 500 products, the client is expected to call this repeatedly
     * using the returned offset.
     */
    public static function ajax_rule_apply()
    {
        check_ajax_referer('tigon_dms_pricing_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized', 403);

        ignore_user_abort(true);
        set_time_limit(270);

        $rule_id = sanitize_text_field($_POST['id'] ?? '');
        $rules   = self::get_rules();
        if (!$rule_id || !isset($rules[$rule_id])) {
            wp_send_json_error('Rule not found.');
        }
        $rule = $rules[$rule_id];

        $ids = self::find_product_ids(
            (int) $rule['manufacturer_id'],
            (int) $rule['model_id'],
            (int) $rule['inventory_status_id'],
            (int) $rule['year']
        );

        $updated = 0;
        $errors  = 0;
        $source  = 'rule:' . $rule_id;
        foreach ($ids as $pid) {
            try {
                if (self::apply_price_to_product((int) $pid, (float) $rule['price'], (float) $rule['sale_price'], $source)) {
                    $updated++;
                } else {
                    $errors++;
                }
            } catch (\Throwable $e) {
                $errors++;
            }
        }

        // Record application stats on the rule
        $rules[$rule_id]['last_applied_at']    = time();
        $rules[$rule_id]['last_applied_count'] = $updated;
        self::save_rules($rules);

        if (function_exists('wc_update_product_lookup_tables')) {
            wc_update_product_lookup_tables();
        }

        wp_send_json_success([
            'id'                 => $rule_id,
            'matched'            => count($ids),
            'updated'            => $updated,
            'errors'             => $errors,
            'last_applied_at'    => $rules[$rule_id]['last_applied_at'],
            'last_applied_count' => $updated,
        ]);
    }

    /**
     * AJAX: Release a rule's lock on all products it manages.
     * Deletes the _tigon_price_source meta so the next DMS sync
     * will restore the original DMS price on those products.
     */
    public static function ajax_rule_release()
    {
        check_ajax_referer('tigon_dms_pricing_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized', 403);

        $rule_id = sanitize_text_field($_POST['id'] ?? '');
        if (!$rule_id) wp_send_json_error('Rule ID required.');

        global $wpdb;
        $needle   = 'rule:' . $rule_id;
        $released = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s",
            self::SOURCE_META,
            $needle
        ));

        wp_send_json_success(['id' => $rule_id, 'released' => (int) $released]);
    }

    /**
     * AJAX: Release ALL bulk-update locks (products tagged 'bulk').
     */
    public static function ajax_bulk_release()
    {
        check_ajax_referer('tigon_dms_pricing_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error('Unauthorized', 403);

        global $wpdb;
        $released = $wpdb->query($wpdb->prepare(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = 'bulk'",
            self::SOURCE_META
        ));

        wp_send_json_success(['released' => (int) $released]);
    }

    /* ─── Rule matching (called by DMS sync auto-reapply) ──────── */

    /**
     * Find the first pricing rule that matches the given product.
     * Returns the rule array or null. Called after every DMS sync
     * create/update so rule prices automatically win on new products.
     *
     * "Match" means every non-zero filter on the rule matches the
     * product: manufacturer term, model term, inventory-status term,
     * and year meta.
     *
     * @param int $product_id
     * @return array|null
     */
    public static function find_matching_rule(int $product_id): ?array
    {
        $rules = self::get_rules();
        if (empty($rules)) return null;

        // Cache product taxonomy terms + year for the duration of this call
        $mfr_terms = wp_get_object_terms($product_id, 'manufacturers', ['fields' => 'ids']);
        $mdl_terms = wp_get_object_terms($product_id, 'models', ['fields' => 'ids']);
        $inv_terms = wp_get_object_terms($product_id, 'inventory-status', ['fields' => 'ids']);
        if (is_wp_error($mfr_terms)) $mfr_terms = [];
        if (is_wp_error($mdl_terms)) $mdl_terms = [];
        if (is_wp_error($inv_terms)) $inv_terms = [];

        $year = (int) get_post_meta($product_id, 'year', true);

        // Score rules: more specific (more filters) wins when multiple match
        $best        = null;
        $best_score  = -1;
        foreach ($rules as $rule) {
            $score = 0;
            if ($rule['manufacturer_id'] > 0) {
                if (!in_array((int) $rule['manufacturer_id'], array_map('intval', $mfr_terms), true)) continue;
                $score++;
            }
            if ($rule['model_id'] > 0) {
                if (!in_array((int) $rule['model_id'], array_map('intval', $mdl_terms), true)) continue;
                $score++;
            }
            if ($rule['inventory_status_id'] > 0) {
                if (!in_array((int) $rule['inventory_status_id'], array_map('intval', $inv_terms), true)) continue;
                $score++;
            }
            if ($rule['year'] > 0) {
                if ((int) $rule['year'] !== $year) continue;
                $score++;
            }
            if ($score > $best_score) {
                $best_score = $score;
                $best       = $rule;
            }
        }
        return $best;
    }

    /**
     * Apply any matching pricing rule to a product. Returns the rule
     * ID that was applied, or null if no rule matched. Used by the
     * DMS sync auto-reapply hook after ensure_woo_product() succeeds.
     */
    public static function apply_matching_rule(int $product_id): ?string
    {
        $rule = self::find_matching_rule($product_id);
        if (!$rule) return null;
        self::apply_price_to_product(
            $product_id,
            (float) $rule['price'],
            (float) ($rule['sale_price'] ?? 0),
            'rule:' . $rule['id']
        );
        return $rule['id'];
    }
}
