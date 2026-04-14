<?php

namespace Tigon\DmsConnect\Admin;

/**
 * Pre-Sync Health Check.
 *
 * Runs a battery of tests against the plugin's configuration and
 * environment to verify that a full DMS → WooCommerce sync will
 * succeed (or at least not silently fail). Intended to be run
 * before a fresh sync, especially after deleting all products.
 *
 * Each check returns one of:
 *   'pass'  — everything good
 *   'warn'  — works but degraded (e.g. missing optional config)
 *   'fail'  — sync will break or produce wrong results
 *
 * The AJAX entry point is Pre_Sync_Check::ajax_run().
 */
final class Pre_Sync_Check
{
    private function __construct() {}

    /** AJAX callback invoked by the Sync page "Run Health Check" button. */
    public static function ajax_run()
    {
        check_ajax_referer('tigon_dms_health_check_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        $results = [];

        $results[] = self::check_woocommerce();
        $results[] = self::check_dms_credentials();
        $results[] = self::check_dms_api_reachable();
        $results[] = self::check_file_source();
        $results[] = self::check_placeholder_image();
        $results[] = self::check_required_taxonomies();
        $results[] = self::check_required_categories();
        $results[] = self::check_store_locations();
        $results[] = self::check_schema_templates();
        $results[] = self::check_cron();
        $results[] = self::check_field_mappings();
        $results[] = self::check_pricing_rules();
        $results[] = self::check_custom_tables();

        // Summarise
        $counts = ['pass' => 0, 'warn' => 0, 'fail' => 0];
        foreach ($results as $r) {
            if (isset($counts[$r['status']])) {
                $counts[$r['status']]++;
            }
        }

        wp_send_json_success([
            'results'  => $results,
            'counts'   => $counts,
            'overall'  => $counts['fail'] > 0 ? 'fail' : ($counts['warn'] > 0 ? 'warn' : 'pass'),
        ]);
    }

    /* ────────── Individual checks ────────── */

    private static function check_woocommerce(): array
    {
        if (class_exists('WooCommerce')) {
            return self::result('pass', 'WooCommerce active', 'WooCommerce is loaded and available.');
        }
        return self::result('fail', 'WooCommerce active',
            'WooCommerce is NOT active. The sync cannot create or update products until WooCommerce is activated.');
    }

    private static function check_dms_credentials(): array
    {
        $url   = function_exists('tigon_dms_get_config') ? tigon_dms_get_config('dms_url', '') : '';
        $user  = function_exists('tigon_dms_get_config') ? tigon_dms_get_config('user_token', '') : '';
        $auth  = function_exists('tigon_dms_get_config') ? tigon_dms_get_config('auth_token', '') : '';

        $missing = [];
        if (empty($url))  $missing[] = 'dms_url';
        if (empty($user)) $missing[] = 'user_token';

        if (!empty($missing)) {
            return self::result('warn', 'DMS credentials',
                'Missing: ' . implode(', ', $missing) . '. Sync will fall back to the public DMS API (read-only, no auth). Configure these in Settings for authenticated access.',
                ['dms_url' => $url ? 'set' : 'empty', 'user_token' => $user ? 'set' : 'empty', 'auth_token' => $auth ? 'set' : 'empty']);
        }
        return self::result('pass', 'DMS credentials',
            'dms_url and user_token are configured.' . ($auth ? ' Auth token also present.' : ' Auth token will be regenerated on next call.'));
    }

    private static function check_dms_api_reachable(): array
    {
        // Light ping — attempt a 1-cart lookup so we know the API answers.
        if (!class_exists('\DMS_API')) {
            return self::result('warn', 'DMS API reachable',
                'DMS_API class not loaded. Cannot verify API connectivity.');
        }
        try {
            $carts = \DMS_API::get_carts(1, 1);
            if (is_array($carts)) {
                return self::result('pass', 'DMS API reachable',
                    'Successfully fetched a test page from the DMS API (' . count($carts) . ' cart(s) returned).');
            }
            return self::result('fail', 'DMS API reachable',
                'DMS API returned a non-array response. Check DMS URL + credentials.');
        } catch (\Throwable $e) {
            return self::result('fail', 'DMS API reachable',
                'DMS API call threw an exception: ' . $e->getMessage());
        }
    }

    private static function check_file_source(): array
    {
        $src = function_exists('tigon_dms_get_file_source') ? tigon_dms_get_file_source() : '';
        if (empty($src)) {
            return self::result('fail', 'Image file source',
                'file_source is empty. Products will sync WITHOUT images until you configure the S3 bucket URL under Settings.');
        }
        if (!preg_match('~^https?://~i', $src)) {
            return self::result('warn', 'Image file source',
                'file_source is set but does not start with http(s)://: "' . esc_html($src) . '". Images may fail to download.');
        }
        return self::result('pass', 'Image file source',
            'Configured: ' . esc_html($src));
    }

    private static function check_placeholder_image(): array
    {
        $placeholder_id = \Tigon\DmsConnect\Includes\Product_Media::PLACEHOLDER_IMAGE_ID;
        $post = get_post($placeholder_id);
        if (!$post || $post->post_type !== 'attachment') {
            return self::result('warn', 'Placeholder image',
                'Attachment ID ' . $placeholder_id . ' does not exist. Products without DMS images will appear with no featured image until you upload a placeholder at that ID or change Product_Media::PLACEHOLDER_IMAGE_ID.');
        }
        return self::result('pass', 'Placeholder image',
            'Attachment #' . $placeholder_id . ' exists: ' . esc_html(get_the_title($placeholder_id)));
    }

    private static function check_required_taxonomies(): array
    {
        $required = ['manufacturers', 'models', 'inventory-status', 'product_cat', 'drivetrain', 'vehicle-class'];
        $missing  = [];
        foreach ($required as $slug) {
            if (!taxonomy_exists($slug)) {
                $missing[] = $slug;
            }
        }
        if (!empty($missing)) {
            return self::result('fail', 'Required taxonomies',
                'Not registered: ' . implode(', ', $missing) . '. Re-save plugin settings or deactivate/reactivate to force taxonomy registration.');
        }
        return self::result('pass', 'Required taxonomies',
            count($required) . ' taxonomies registered: ' . implode(', ', $required));
    }

    private static function check_required_categories(): array
    {
        $required = [
            ['Golf Carts',     0],
            ['New',            null],
            ['Used',           null],
            ['2x4',            null],
            ['4x4',            null],
            ['Lifted',         null],
            ['NON-Lifted',     null],
            ['Electric',       null],
            ['Gas',            null],
        ];

        $missing = [];
        $found   = 0;
        foreach ($required as $r) {
            list($name, $parent) = $r;
            $term = get_term_by('name', $name, 'product_cat');
            if ($term) {
                $found++;
            } else {
                $missing[] = $name;
            }
        }

        if (!empty($missing)) {
            return self::result('warn', 'Required product categories',
                'Found ' . $found . '/' . count($required) . '. Missing: ' . implode(', ', $missing) . '. Missing categories are skipped silently during sync (products still created, just not categorised into those branches).',
                ['missing' => $missing]);
        }
        return self::result('pass', 'Required product categories',
            'All ' . count($required) . ' expected category names present.');
    }

    private static function check_store_locations(): array
    {
        if (!class_exists('\Tigon\DmsConnect\Admin\Attributes')) {
            return self::result('warn', 'Store locations',
                'Attributes class not loaded. Cannot verify location map.');
        }
        $count = is_array(\Tigon\DmsConnect\Admin\Attributes::$locations ?? null)
            ? count(\Tigon\DmsConnect\Admin\Attributes::$locations)
            : 0;
        if ($count === 0) {
            return self::result('fail', 'Store locations',
                'Attributes::$locations is empty. Sync will fall back to DMS API lookup per cart (slow) or mark products draft.');
        }
        return self::result('pass', 'Store locations',
            $count . ' store locations mapped (T0–T' . ($count - 1) . ').');
    }

    private static function check_schema_templates(): array
    {
        if (!function_exists('tigon_dms_get_schema_templates')) {
            return self::result('warn', 'Schema templates',
                'tigon_dms_get_schema_templates() not loaded — cannot verify.');
        }
        $tpl = tigon_dms_get_schema_templates();
        $keys = ['schema_name', 'schema_slug', 'schema_image_name'];
        $set  = [];
        $unset = [];
        foreach ($keys as $k) {
            if (!empty($tpl[$k])) { $set[] = $k; } else { $unset[] = $k; }
        }
        if (empty($unset)) {
            return self::result('pass', 'Schema templates',
                'All custom templates set: ' . implode(', ', $set));
        }
        if (empty($set)) {
            return self::result('warn', 'Schema templates',
                'No custom templates configured. Sync will use built-in defaults (fine for most installs).');
        }
        return self::result('warn', 'Schema templates',
            count($set) . ' set, ' . count($unset) . ' using defaults (' . implode(', ', $unset) . ')');
    }

    private static function check_cron(): array
    {
        $next = wp_next_scheduled('tigon_dms_sync_inventory');
        if (!$next) {
            return self::result('warn', 'Scheduled sync cron',
                'No scheduled DMS sync is queued. Manual sync from the Sync page works, but automatic background sync is not running. Re-save sync interval to schedule.');
        }
        $when = date_i18n('Y-m-d H:i:s', $next);
        return self::result('pass', 'Scheduled sync cron',
            'Next run: ' . $when);
    }

    private static function check_field_mappings(): array
    {
        global $wpdb;
        $table = $wpdb->prefix . 'tigon_dms_field_mappings';
        $exists = $wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table));
        if (!$exists) {
            return self::result('warn', 'Field mappings table',
                'Table ' . $table . ' does not exist. Custom field mappings will be skipped (core DMS → WC mappings still run).');
        }
        $count = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table}");
        $enabled = (int) $wpdb->get_var("SELECT COUNT(*) FROM {$table} WHERE is_enabled = 1");
        return self::result('pass', 'Field mappings',
            $count . ' total mapping(s), ' . $enabled . ' enabled.');
    }

    private static function check_pricing_rules(): array
    {
        if (!class_exists('\Tigon\DmsConnect\Admin\Pricing_Page')) {
            return self::result('warn', 'Pricing rules',
                'Pricing_Page not loaded.');
        }
        $rules = \Tigon\DmsConnect\Admin\Pricing_Page::get_rules();
        $count = count($rules);
        if ($count === 0) {
            return self::result('pass', 'Pricing rules',
                'No rules configured. DMS prices will flow through unchanged.');
        }
        return self::result('pass', 'Pricing rules',
            $count . ' rule(s) configured. Matching products will be priced by rule after sync.');
    }

    private static function check_custom_tables(): array
    {
        global $wpdb;
        $expected = [
            $wpdb->prefix . 'tigon_dms_config',
            $wpdb->prefix . 'tigon_dms_field_mappings',
            $wpdb->prefix . 'tigon_dms_carts',
        ];
        $missing = [];
        foreach ($expected as $t) {
            if (!$wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $t))) {
                $missing[] = $t;
            }
        }
        if (!empty($missing)) {
            return self::result('fail', 'Plugin database tables',
                'Missing tables: ' . implode(', ', $missing) . '. Deactivate + reactivate the plugin to recreate them.');
        }
        return self::result('pass', 'Plugin database tables',
            count($expected) . ' plugin tables exist.');
    }

    /* ────────── Helper ────────── */

    private static function result(string $status, string $name, string $message, array $extra = []): array
    {
        return [
            'status'  => $status,   // pass | warn | fail
            'name'    => $name,
            'message' => $message,
            'extra'   => $extra,
        ];
    }
}
