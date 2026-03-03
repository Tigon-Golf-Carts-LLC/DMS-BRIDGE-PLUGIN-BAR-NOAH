<?php

namespace Tigon\DmsConnect\Admin;

use Tigon\DmsConnect\Includes\DMS_Connector;
use Tigon\DmsConnect\Includes\Product_Fields;

class Admin_Page
{

    private function __construct()
    {
    }

    /**
     * Add Tigon DMS Connect to Admin Sidebar
     * @return void
     */
    public static function add_menu_page()
    {
        $svg = file_get_contents(TIGON_DMS_PLUGIN_DIR . 'assets/images/tigondb.svg');
        $data_uri = 'data:image/svg+xml;base64,' . base64_encode($svg);

        $page_title = "Tigon DMS Connect";
        $menu_title = "DMS Connect";
        $capability = "manage_options";
        $menu_slug = "tigon-dms-connect";
        $callback = 'Tigon\DmsConnect\Admin\Admin_Page::diagnostic_page';
        $icon_url = $data_uri;
        $position = 55;
        add_menu_page($page_title, $menu_title, $capability, $menu_slug, $callback, $icon_url, $position);
        self::add_settings_page();
        self::add_field_mapping_page();
    }


    /**
     * Add Settings submenu
     * @return void
     */
    public static function add_settings_page()
    {
        $parent_slug = "tigon-dms-connect";
        $page_title = "Tigon DMS Settings";
        $menu_title = "Settings";
        $capability = "manage_options";
        $menu_slug = "settings";
        $callback = 'Tigon\DmsConnect\Admin\Admin_Page::settings_page';
        add_submenu_page($parent_slug, $page_title, $menu_title, $capability, $menu_slug, $callback);
    }

    /**
     * Add Field Mapping submenu
     */
    public static function add_field_mapping_page()
    {
        add_submenu_page(
            'tigon-dms-connect',
            'DMS Field Mapping',
            'Field Mapping',
            'manage_options',
            'field-mapping',
            'Tigon\DmsConnect\Admin\Admin_Page::field_mapping_page'
        );
    }

    public static function page_header()
    {
        $css = file_get_contents(__DIR__ . '/../../assets/css/admin.css');
        $svg = preg_replace(
            '/#FFFFFF/',
            '#F4F4F9',
            file_get_contents(__DIR__ . '/../../assets/images/tigondb.svg')
        );

        echo '
        <meta name="viewport" content="height=device-height, width=device-width, initial-scale=1.0, minimum-scale=1.0, target-densitydpi=device-dpi">
        <style>' . $css . '</style>

        <div class="header">
            <span class="tigon-dms-icon">' . $svg . '</span>
            <h1>Tigon DMS Connect</h1>
        </div>';
    }

    public static function diagnostic_page()
    {
    ### Database Operations ###

        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        
        // Get new carts of inventory type ACTIVE NEW INVENTORY as Slugs
        $new_carts = $wpdb->get_results('
            SELECT post_name
            FROM `'.$wpdb->prefix.'posts`
            WHERE ID IN
            (
                SELECT object_id FROM '.$wpdb->prefix.'term_relationships
                WHERE term_taxonomy_id = 4549
            )
            AND ID IN
            (
                SELECT post_id FROM '.$wpdb->prefix.'postmeta
                WHERE meta_key = "_stock_status"
                AND meta_value = "instock"
            )
            ORDER BY ID ASC;
        ');
        $new_carts = array_map(function($cart) {
            return $cart->post_name;
        }, $new_carts);

        // Get used carts of inventory type ACTIVE USED INVENTORY as SKUs
        $used_carts = $wpdb->get_results('
            SELECT meta_value
            FROM `'.$wpdb->prefix.'postmeta`
            WHERE post_id IN
            (
                SELECT * FROM
                (
                    SELECT object_id FROM
                    `'.$wpdb->prefix.'term_relationships` WHERE '.$wpdb->prefix.'term_relationships.term_taxonomy_id = 4553
                ) AS subquery
            )
            AND meta_key = "_sku";
        ');
        $used_carts = array_map(function($cart) {
            return $cart->meta_value;
        }, $used_carts);

    ### New Operations ###

        // Get new cart data from DMS
        // Result is encoded to string for array_unique
        $dms_new = json_decode(DMS_Connector::request('{"isUsed":false, "needOnWebsite": true, "isInStock": true, "isInBoneyard": false}', '/chimera/lookup', 'POST'), true)??[];
        $dms_new_unique = array_map(function($cart) {
            if (str_contains(strtoupper($cart['serialNo']), 'DELETE') || str_contains(strtoupper($cart['vinNo']), 'DELETE'))
                return '--DELETE--';

            $location_id = $cart['cartLocation']['locationId'];
            if (!isset(Attributes::$locations[$location_id])) {
                return '--DELETE--';
            }
            $loc = Attributes::$locations[$location_id];
            $city = $loc['city_short'] ?? $loc['city'];

            $make = preg_replace('/\s+/', '-', trim(preg_replace('/\+/', ' plus ', $cart['cartType']['make'])));
            $model = preg_replace('/\s+/', '-', trim(preg_replace('/\+/', ' plus ', $cart['cartType']['model'])));
            $color = preg_replace('/\s+/', '-', $cart['cartAttributes']['cartColor']);
            $seat = preg_replace('/\s+/', '-', $cart['cartAttributes']['seatColor']);
            $location = preg_replace('/\s+/', '-', $city . "-" . $loc['st']);
            $year = preg_replace('/\s+/', '-', $cart['cartType']['year']);
            return json_encode(['url' => strtolower("$make-$model-$color-seat-$seat-$location"), 'year' => $year]);
        }, $dms_new);
        // Remove invalid carts
        $dms_new_unique = array_filter($dms_new_unique, function($cart) {
            return ($cart !== '--DELETE--');
        });
        // Remove duplicate cart types
        $dms_new_unique = array_unique($dms_new_unique);

        // Decode filtered carts so we can work with their data
        $dms_new_unique = array_map(function($cart) {
            return json_decode($cart, true);
        }, $dms_new_unique);

        // Format urls such that the newest cart of each type does not have the year appended
        $dms_new_unique = array_map(function($cart) use (&$dms_new_unique){
            $should_have_year = false;
            // Loop through each other cart
            if(gettype($cart) === 'array') foreach ($dms_new_unique as &$other) {
                // If it hasn't been formatted already
                if (gettype($other) === 'array') {
                    // If it's the same type
                    if ($other['url'] === $cart['url']) {
                        // If other is older, append year to other
                        if ($cart['year'] > $other['year']) {
                            $other = $other['url'] . '-' . $other['year'];
                        // If self is older, append year to self
                        } else if ($cart['year'] < $other['year']) {
                            $should_have_year = true;
                        }
                    }
                }
            }
            return $cart['url'] . ($should_have_year?('-'.$cart['year']):'');
        }, $dms_new_unique);
        
        // Carts on site which are not on DMS
        $extra_new_pids = $new_carts;
        foreach($dms_new_unique as $url) {
            $pos = array_search($url, $extra_new_pids);
            array_splice($extra_new_pids, $pos, 1);
        }
        $extra_new_pids = array_map(function($slug) {
            return get_page_by_path($slug, OBJECT, 'product')->ID;
        }, $extra_new_pids);
        // Carts on DMS which are not on site
        $missing_new_skus = array_values(array_diff($dms_new_unique, $new_carts));

    ### Used Operation ###

        // Get used cart data from DMS
        $dms_used = json_decode(DMS_Connector::request('{"isUsed":true, "isInStock": true, "isInBoneyard": false, "needOnWebsite": true}', '/chimera/lookup', 'POST'), true)??[];
        $dms_used_skus = array_map(function($cart) {
            return $cart['vinNo']?$cart['vinNo']:$cart['serialNo'];
        }, $dms_used);

        $extra_used_pids = array_values(array_diff($used_carts, $dms_used_skus));
        $extra_used_pids = array_map(function($sku) {
            return wc_get_product_id_by_sku( $sku );
        }, $extra_used_pids);
        $missing_used_skus = array_values(array_diff($dms_used_skus, $used_carts));

    ### Data Parsing ###
        
        $new_cart_pages = count($new_carts);
        $new_not_on_site = count($missing_new_skus);
        $new_not_on_dms = count($extra_new_pids);
        $new_cart_dms = count($dms_new_unique);

        $new_accurate = ($new_cart_pages - $new_not_on_dms) / ($new_cart_pages + $new_not_on_site) * 100;
        $new_extra = ($new_cart_pages / ($new_cart_pages + $new_not_on_site)) * 100;


        $used_cart_pages = count($used_carts);
        $used_not_on_site = count($missing_used_skus);
        $used_not_on_dms = count($extra_used_pids);
        $used_cart_dms = count($dms_used);

        $used_accurate = ($used_cart_pages - $used_not_on_dms) / ($used_cart_pages + $used_not_on_site) * 100;
        $used_extra = ($used_cart_pages / ($used_cart_pages + $used_not_on_site)) * 100;

    ### HTML Element Formatting ###

        $missing_new_string = '<p>Missing New: [ '.implode(', ', $missing_new_skus).' ]</p>';
        $new_links = [];
        foreach($extra_new_pids as $pid) {
            array_push($new_links,
                '<a href="' .
                site_url() . "/wp-admin/post.php?post=$pid&action=edit&classic-editor" .
                '">' . $pid . '</a>');
        }
        $extra_new_string = '<p>Extra New: [ '.implode(', ', $new_links).' ]</p>';

        $missing_used_string = '<p>Missing Used: [ '.implode(', ', $missing_used_skus).' ]</p>';
        $used_links = [];
        foreach($extra_used_pids as $pid) {
            array_push($used_links,
                '<a href="' .
                site_url() . "/wp-admin/post.php?post=$pid&action=edit&classic-editor" .
                '">' . $pid . '</a>');
        }
        $extra_used_string = '<p>Extra Used: [ '.implode(', ', $used_links).' ]</p>';

    ### Output ###

        self::page_header();

        echo '
        <div class="body" style="flex-direction: column;">
            <div class="action-box-group">
                <div class="action-box primary" id="new-status">
                    <h2>New Carts</h2>
                    <p>Pages on site: '.$new_cart_pages.'</p>
                    <p>URLs on DMS: '.$new_cart_dms.'</p>
                    <p>Missing site pages: '.$new_not_on_site.'</p>
                    <p>Extra site pages: '.$new_not_on_dms.'</p>
                </div>
                <div class="action-box secondary" id="used-status">
                    '.$missing_new_string.'
                    '.$extra_new_string.'
                </div>
                <div class="action-box primary" id="new-chart" style="flex-direction:row; gap: 2.5rem">
                    <div class="action-box-column chart-container">
                        <div class="pie-chart" style="--percent-accurate: '.$new_accurate.'%; --percent-extra: '.$new_extra.'%;">
                            New Carts
                        </div>
                    </div>
                    <div class="action-box-column chart-legend">
                        <div style="--color: var(--good)">Inventory on site and DMS</div>
                        <div style="--color: var(--warning)">Inventory not on DMS</div>
                        <div style="--color: var(--bad)">Inventory not on site</div>
                    </div>
                </div>
            </div>
            <div class="action-box-group">
                <div class="action-box primary" id="used-status">
                    <h2>Used Carts</h2>
                    <p>Pages on site: '.$used_cart_pages.'</p>
                    <p>Carts on DMS: '.$used_cart_dms.'</p>
                    <p>Missing site pages: '.$used_not_on_site.'</p>
                    <p>Extra site pages: '.$used_not_on_dms.'</p>
                </div>
                <div class="action-box secondary" id="used-status">
                    '.$missing_used_string.'
                    '.$extra_used_string.'
                </div>
                <div class="action-box primary" id="used-chart" style="flex-direction:row; gap: 2.5rem">
                    <div class="action-box-column chart-container">
                        <div class="pie-chart" style="--percent-accurate: '.$used_accurate.'%; --percent-extra: '.$used_extra.'%;">
                            Used Carts
                        </div>
                    </div>
                    <div class="action-box-column chart-legend">
                        <div style="--color: var(--good)">Inventory on site and DMS</div>
                        <div style="--color: var(--warning)">Inventory not on DMS</div>
                        <div style="--color: var(--bad)">Inventory not on site</div>
                    </div>
                </div>
            </div>
        </div>
        ';
    }


    /**
     * Field Mapping admin page.
     *
     * Comprehensive DMS-to-WooCommerce field mapping editor with:
     * - Feed Explorer: browse all incoming DMS fields with types and examples
     * - Mapping Editor: CRUD for field mapping rules with add/edit form
     * - Target Browser: all available WooCommerce targets organized by type
     */
    public static function field_mapping_page()
    {
        // Ensure table exists (handles upgrades from older versions)
        \Tigon\DmsConnect\Admin\Field_Mapping::install();

        $nonce       = wp_create_nonce('tigon_dms_field_mapping_nonce');
        $mappings    = \Tigon\DmsConnect\Admin\Field_Mapping::get_all();
        $dms_fields  = \Tigon\DmsConnect\Admin\Field_Mapping::get_known_dms_fields();
        $woo_targets = \Tigon\DmsConnect\Admin\Field_Mapping::get_known_woo_targets();

        $transforms = [
            'direct'        => 'Direct (pass-through)',
            'uppercase'     => 'UPPERCASE',
            'lowercase'     => 'lowercase',
            'ucwords'       => 'Ucwords (Title Case)',
            'boolean_yesno' => 'Boolean &rarr; Yes/No',
            'boolean_label' => 'Boolean &rarr; Custom Labels',
            'prefix'        => 'Prefix (prepend text)',
            'suffix'        => 'Suffix (append text)',
            'template'      => 'Template ({value} placeholder)',
            'static'        => 'Static Value',
        ];

        // Load mock.json for feed example values
        $mock_data = [];
        $mock_path = defined('TIGON_DMS_PLUGIN_DIR')
            ? TIGON_DMS_PLUGIN_DIR . 'assets/mock.json'
            : __DIR__ . '/../../assets/mock.json';
        if (file_exists($mock_path)) {
            $raw = file_get_contents($mock_path);
            $parsed = json_decode($raw, true);
            if (is_array($parsed) && !empty($parsed)) {
                $mock_data = $parsed[0];
            }
        }

        // Build mapping index: dms_path → mapping row
        $mapping_index = [];
        foreach ($mappings as $m) {
            $mapping_index[$m['dms_path']] = $m;
        }

        // Group DMS fields by top-level parent
        $group_labels = [
            'cartType'       => 'Cart Type',
            'cartAttributes' => 'Cart Attributes',
            'addedFeatures'  => 'Added Features',
            'options'        => 'Options (Add-ons)',
            'battery'        => 'Battery',
            'engine'         => 'Engine',
            'cartLocation'   => 'Location',
            'title'          => 'Title / Legal',
            'rfsStatus'      => 'RFS Status',
            'floorPlanned'   => 'Floor Plan',
            'advertising'    => 'Advertising',
            '_toplevel'      => 'Top-Level Fields',
        ];
        $field_groups = [];
        foreach ($dms_fields as $f) {
            $parts = explode('.', $f, 2);
            $group_key = count($parts) > 1 ? $parts[0] : '_toplevel';
            $field_groups[$group_key][] = $f;
        }

        // Coverage stats
        $total_fields  = count($dms_fields);
        $mapped_count  = 0;
        foreach ($dms_fields as $f) {
            if (isset($mapping_index[$f])) {
                $mapped_count++;
            }
        }
        $unmapped_count = $total_fields - $mapped_count;
        $coverage_pct   = $total_fields > 0 ? round(($mapped_count / $total_fields) * 100) : 0;

        // Flat list of all WooCommerce target keys for "in-use" checks
        $all_woo_flat = [];
        foreach ($woo_targets as $targets) {
            foreach ($targets as $t) {
                $all_woo_flat[$t] = false;
            }
        }
        foreach ($mappings as $m) {
            $all_woo_flat[$m['woo_target']] = true;
        }

        self::page_header();

        // ── Inline styles (shared dbo-* system) ─────────────────────
        echo '<style>
        .dbo-wrap{display:flex;flex-direction:column;width:92%;max-width:1400px;margin:1.5rem auto;color:var(--font-dark);}
        .dbo-tabs{display:flex;gap:0;border-bottom:3px solid var(--main-color);margin-bottom:0;flex-wrap:wrap;}
        .dbo-tab{padding:0.65rem 1.2rem;background:var(--content-color);border:1px solid #ccc;border-bottom:none;
                  border-radius:0.4rem 0.4rem 0 0;cursor:pointer;font-size:0.85rem;font-weight:600;color:var(--font-dark);
                  transition:background 0.15s,color 0.15s;user-select:none;margin-right:2px;}
        .dbo-tab:hover{background:#e8e8e8;}
        .dbo-tab.active{background:var(--main-color);color:#fff;border-color:var(--main-color);}
        .dbo-panel{display:none;background:var(--content-color);border:1px solid #ddd;border-top:none;
                   border-radius:0 0 0.5rem 0.5rem;padding:1.5rem;box-shadow:0 2px 6px rgba(0,0,0,0.08);}
        .dbo-panel.active{display:block;}
        .dbo-search{width:100%;padding:0.5rem 0.75rem;border:1px solid #ccc;border-radius:0.35rem;font-size:0.85rem;
                    margin-bottom:1rem;box-sizing:border-box;}
        .dbo-search:focus{outline:none;border-color:var(--accent-color);box-shadow:0 0 0 2px rgba(85,116,134,0.2);}
        .dbo-table{width:100%;border-collapse:collapse;font-size:0.82rem;}
        .dbo-table th{background:var(--main-color);color:#fff;padding:0.55rem 0.75rem;text-align:left;
                      position:sticky;top:0;z-index:2;font-weight:600;white-space:nowrap;}
        .dbo-table td{padding:0.45rem 0.75rem;border-bottom:1px solid #e0e0e0;vertical-align:top;}
        .dbo-table tr:hover td{background:rgba(156,52,52,0.04);}
        .dbo-table code{background:#f0f0f0;padding:0.1rem 0.35rem;border-radius:3px;font-size:0.8rem;}
        .dbo-scroll{max-height:500px;overflow-y:auto;border:1px solid #ddd;border-radius:0.35rem;}
        .dbo-scroll::-webkit-scrollbar{width:8px;}
        .dbo-scroll::-webkit-scrollbar-thumb{background:#bbb;border-radius:4px;}
        .dbo-badge{display:inline-block;padding:0.15rem 0.55rem;border-radius:1rem;font-size:0.72rem;
                   font-weight:700;color:#fff;white-space:nowrap;}
        .dbo-badge.green{background:#39c939;} .dbo-badge.red{background:#cf1010;}
        .dbo-badge.blue{background:#3b82f6;} .dbo-badge.orange{background:#e67e22;}
        .dbo-badge.gray{background:#808080;} .dbo-badge.purple{background:#8b5cf6;}
        .dbo-badge.teal{background:#0d9488;}
        .dbo-cards{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;margin-bottom:1.5rem;}
        .dbo-card{background:#fff;border:1px solid #ddd;border-radius:0.5rem;padding:1rem;text-align:center;
                  box-shadow:0 1px 3px rgba(0,0,0,0.06);transition:transform 0.15s,box-shadow 0.15s;}
        .dbo-card:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,0.1);}
        .dbo-card .num{font-size:1.8rem;font-weight:800;color:var(--main-color);line-height:1.1;}
        .dbo-card .lbl{font-size:0.78rem;color:#666;margin-top:0.3rem;}
        .dbo-section{margin-bottom:1rem;}
        .dbo-section-hd{display:flex;align-items:center;gap:0.5rem;cursor:pointer;padding:0.6rem 0.75rem;
                        background:#fff;border:1px solid #ddd;border-radius:0.4rem;margin-bottom:0.5rem;user-select:none;
                        transition:background 0.15s;}
        .dbo-section-hd:hover{background:#f5f5f5;}
        .dbo-section-hd .arrow{transition:transform 0.2s;font-size:0.75rem;}
        .dbo-section-hd.open .arrow{transform:rotate(90deg);}
        .dbo-section-hd h3{margin:0;font-size:0.95rem;flex:1;}
        .dbo-section-bd{display:none;}
        .dbo-section-hd.open + .dbo-section-bd{display:block;}
        .dbo-pill-row{display:flex;flex-wrap:wrap;gap:0.5rem;margin-bottom:1rem;}
        .dbo-pill{padding:0.25rem 0.65rem;border-radius:2rem;border:1px solid #ccc;font-size:0.75rem;
                  cursor:pointer;user-select:none;transition:all 0.15s;background:#fff;}
        .dbo-pill:hover{border-color:var(--main-color);color:var(--main-color);}
        .dbo-pill.active{background:var(--main-color);color:#fff;border-color:var(--main-color);}
        .dbo-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:1rem;}
        @media(max-width:900px){.dbo-grid-2{grid-template-columns:1fr;} .dbo-cards{grid-template-columns:repeat(2,1fr);}}
        .dbo-empty{padding:2rem;text-align:center;color:#999;font-style:italic;}

        /* Field mapping form styles */
        .fm-form{display:grid;grid-template-columns:1fr 1fr;gap:0.75rem 1.5rem;max-width:900px;margin-top:1rem;
                 padding:1.2rem;background:#fff;border:1px solid #ddd;border-radius:0.4rem;}
        .fm-form label{font-weight:600;font-size:0.82rem;margin-bottom:0.2rem;display:block;}
        .fm-form select,.fm-form input[type="text"],.fm-form input[type="number"]{
            width:100%;padding:0.4rem 0.5rem;border:1px solid #ccc;border-radius:0.3rem;font-size:0.82rem;box-sizing:border-box;}
        .fm-form select:focus,.fm-form input:focus{outline:none;border-color:var(--accent-color);box-shadow:0 0 0 2px rgba(85,116,134,0.2);}
        .fm-span2{grid-column:span 2;}
        .fm-actions{display:flex;justify-content:flex-end;gap:0.5rem;align-items:center;}
        .fm-btn{padding:0.5rem 1.5rem;border:none;border-radius:0.35rem;font-weight:600;cursor:pointer;font-size:0.82rem;
                transition:all 0.15s;}
        .fm-btn-primary{background:var(--accent-color);color:#fff;}
        .fm-btn-primary:hover{background:#466575;}
        .fm-btn-secondary{background:#e0e0e0;color:#333;}
        .fm-btn-secondary:hover{background:#ccc;}
        .fm-val-preview{max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-size:0.75rem;color:#666;}
        .fm-type-badge{display:inline-block;padding:0.1rem 0.4rem;border-radius:3px;font-size:0.7rem;font-weight:600;
                       background:#e8e8e8;color:#555;}
        .fm-type-badge.string{background:#dbeafe;color:#1e40af;}
        .fm-type-badge.number{background:#fef3c7;color:#92400e;}
        .fm-type-badge.boolean{background:#d1fae5;color:#065f46;}
        .fm-type-badge.array{background:#ede9fe;color:#5b21b6;}
        .fm-type-badge.null{background:#f3f4f6;color:#6b7280;}
        .fm-type-badge.object{background:#fce7f3;color:#9d174d;}
        .fm-toggle{position:relative;display:inline-block;width:36px;height:20px;}
        .fm-toggle input{opacity:0;width:0;height:0;}
        .fm-toggle .slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#ccc;
                           border-radius:20px;transition:0.2s;}
        .fm-toggle .slider:before{position:absolute;content:"";height:14px;width:14px;left:3px;bottom:3px;
                                   background:#fff;border-radius:50%;transition:0.2s;}
        .fm-toggle input:checked + .slider{background:#39c939;}
        .fm-toggle input:checked + .slider:before{transform:translateX(16px);}
        .fm-arrow-col{text-align:center;color:var(--main-color);font-weight:700;font-size:1.1rem;}
        </style>';

        // ── Tab structure ────────────────────────────────────────────
        echo '<div class="dbo-wrap">';

        $tabs = [
            'feed'     => 'Feed Explorer',
            'mappings' => 'Mapping Editor',
            'targets'  => 'Target Browser',
        ];
        echo '<div class="dbo-tabs">';
        $first = true;
        foreach ($tabs as $id => $label) {
            echo '<div class="dbo-tab' . ($first ? ' active' : '') . '" data-tab="' . esc_attr($id) . '">' . esc_html($label) . '</div>';
            $first = false;
        }
        echo '</div>';

        // ═════════════════════════════════════════════════════════════
        // TAB 1: FEED EXPLORER
        // ═════════════════════════════════════════════════════════════
        echo '<div class="dbo-panel active" data-panel="feed">';
        echo '<h2 style="margin-top:0;">DMS Inventory Feed Structure</h2>';
        echo '<p style="color:#666;font-size:0.85rem;">Complete breakdown of every field in the incoming DMS API feed. Shows data types, example values from mock data, and current mapping status.</p>';

        // Coverage cards
        echo '<div class="dbo-cards">';
        echo '<div class="dbo-card"><div class="num">' . esc_html($total_fields) . '</div><div class="lbl">Total Feed Fields</div></div>';
        echo '<div class="dbo-card"><div class="num">' . esc_html($mapped_count) . '</div><div class="lbl">Mapped Fields</div></div>';
        echo '<div class="dbo-card"><div class="num">' . esc_html($unmapped_count) . '</div><div class="lbl">Unmapped Fields</div></div>';
        echo '<div class="dbo-card"><div class="num">' . esc_html($coverage_pct) . '%</div><div class="lbl">Coverage</div></div>';
        echo '</div>';

        // Filter pills + search
        echo '<div class="dbo-pill-row" id="feed-filter-pills">';
        echo '<div class="dbo-pill active" data-feed-filter="all">All Fields</div>';
        echo '<div class="dbo-pill" data-feed-filter="mapped">Mapped</div>';
        echo '<div class="dbo-pill" data-feed-filter="unmapped">Unmapped</div>';
        echo '</div>';
        echo '<input type="text" class="dbo-search" id="feed-search" placeholder="Search feed fields...">';

        // Field groups
        foreach ($field_groups as $group_key => $fields) {
            $group_label = $group_labels[$group_key] ?? ucfirst($group_key);
            $group_mapped = 0;
            foreach ($fields as $f) {
                if (isset($mapping_index[$f])) $group_mapped++;
            }
            $group_total = count($fields);

            echo '<div class="dbo-section feed-section"><div class="dbo-section-hd open" onclick="this.classList.toggle(\'open\')">';
            echo '<span class="arrow">&#9654;</span>';
            echo '<h3>' . esc_html($group_label) . '</h3>';
            echo '<span class="dbo-badge teal">' . esc_html($group_total) . ' fields</span>';
            if ($group_mapped > 0) {
                echo ' <span class="dbo-badge green">' . esc_html($group_mapped) . ' mapped</span>';
            }
            echo '</div><div class="dbo-section-bd">';

            echo '<div class="dbo-scroll" style="max-height:400px;"><table class="dbo-table"><thead><tr>';
            echo '<th>Field Path</th><th>Type</th><th>Example Value</th><th>Status</th><th>Mapped To</th></tr></thead><tbody>';

            foreach ($fields as $f) {
                // Resolve example value from mock data
                $example = \Tigon\DmsConnect\Admin\Field_Mapping::resolve_dms_path($mock_data, $f);
                $type_name = 'null';
                $type_class = 'null';
                if (is_string($example))     { $type_name = 'string';  $type_class = 'string'; }
                elseif (is_bool($example))   { $type_name = 'boolean'; $type_class = 'boolean'; }
                elseif (is_int($example) || is_float($example)) { $type_name = 'number'; $type_class = 'number'; }
                elseif (is_array($example))  { $type_name = 'array';   $type_class = 'array'; }
                elseif (is_null($example))   { $type_name = 'null';    $type_class = 'null'; }

                // Format example value
                $example_display = '';
                if (is_null($example)) {
                    $example_display = '<em style="color:#999;">null</em>';
                } elseif (is_bool($example)) {
                    $example_display = $example ? '<span style="color:#065f46;">true</span>' : '<span style="color:#991b1b;">false</span>';
                } elseif (is_array($example)) {
                    $json = wp_json_encode($example, JSON_UNESCAPED_SLASHES);
                    if (strlen($json) > 60) $json = substr($json, 0, 57) . '...';
                    $example_display = '<code style="font-size:0.72rem;">' . esc_html($json) . '</code>';
                } else {
                    $val = (string) $example;
                    if (strlen($val) > 50) $val = substr($val, 0, 47) . '...';
                    $example_display = esc_html($val);
                }

                // Mapping status
                $is_mapped = isset($mapping_index[$f]);
                $status_badge = $is_mapped
                    ? '<span class="dbo-badge green">Mapped</span>'
                    : '<span class="dbo-badge gray">Unmapped</span>';
                $target_display = $is_mapped
                    ? '<code>' . esc_html($mapping_index[$f]['woo_target']) . '</code>'
                    : '<button class="fm-btn fm-btn-primary" style="padding:0.2rem 0.6rem;font-size:0.72rem;" '
                      . 'onclick="tigonFM.quickMap(\'' . esc_attr($f) . '\')">+ Map</button>';

                echo '<tr class="feed-row" data-mapped="' . ($is_mapped ? '1' : '0') . '">';
                echo '<td><code>' . esc_html($f) . '</code></td>';
                echo '<td><span class="fm-type-badge ' . $type_class . '">' . $type_name . '</span></td>';
                echo '<td class="fm-val-preview">' . $example_display . '</td>';
                echo '<td>' . $status_badge . '</td>';
                echo '<td>' . $target_display . '</td>';
                echo '</tr>';
            }

            echo '</tbody></table></div></div></div>';
        }
        echo '</div>'; // end feed panel

        // ═════════════════════════════════════════════════════════════
        // TAB 2: MAPPING EDITOR
        // ═════════════════════════════════════════════════════════════
        echo '<div class="dbo-panel" data-panel="mappings">';
        echo '<h2 style="margin-top:0;">Active Field Mappings</h2>';
        echo '<p style="color:#666;font-size:0.85rem;">These custom mappings override the built-in sync logic. They are applied during both import and scheduled sync operations.</p>';

        // Active mappings table
        echo '<input type="text" class="dbo-search" id="mapping-search" placeholder="Search mappings...">';
        echo '<div class="dbo-scroll" style="max-height:450px;">';
        echo '<table class="dbo-table" id="tigon-mapping-table"><thead><tr>';
        echo '<th style="width:40px;">#</th>';
        echo '<th>DMS Field Path</th>';
        echo '<th class="fm-arrow-col" style="width:40px;">&rarr;</th>';
        echo '<th>Target Type</th>';
        echo '<th>WooCommerce Target</th>';
        echo '<th>Transform</th>';
        echo '<th>Config</th>';
        echo '<th style="width:70px;">Enabled</th>';
        echo '<th style="width:110px;">Actions</th>';
        echo '</tr></thead><tbody id="tigon-mapping-rows">';

        if (empty($mappings)) {
            echo '<tr id="tigon-no-mappings-row"><td colspan="9" class="dbo-empty">';
            echo 'No custom field mappings configured yet. Add one below or use the defaults.<br>';
            echo '<small style="color:#888;">The built-in mapping logic (categories, attributes, pricing, images) continues to work without custom mappings.</small>';
            echo '</td></tr>';
        } else {
            foreach ($mappings as $m) {
                $mid = intval($m['mapping_id']);
                $enabled_checked = $m['is_enabled'] ? 'checked' : '';
                $type_badge = 'gray';
                if ($m['target_type'] === 'postmeta') $type_badge = 'blue';
                elseif ($m['target_type'] === 'taxonomy') $type_badge = 'purple';
                elseif ($m['target_type'] === 'post') $type_badge = 'teal';

                // Store ALL editable values as data-* attributes so
                // the Edit button JS never has to scrape cell text.
                echo '<tr data-mapping-id="' . $mid . '"'
                    . ' data-dms-path="'      . esc_attr($m['dms_path'])      . '"'
                    . ' data-woo-target="'     . esc_attr($m['woo_target'])    . '"'
                    . ' data-target-type="'    . esc_attr($m['target_type'])   . '"'
                    . ' data-transform="'      . esc_attr($m['transform'])     . '"'
                    . ' data-transform-cfg="'  . esc_attr($m['transform_cfg']) . '"'
                    . ' data-is-enabled="'     . ($m['is_enabled'] ? '1' : '0') . '"'
                    . ' data-sort-order="'     . intval($m['sort_order'])       . '"'
                    . '>';
                echo '<td>' . $mid . '</td>';
                echo '<td><code>' . esc_html($m['dms_path']) . '</code></td>';
                echo '<td class="fm-arrow-col">&rarr;</td>';
                echo '<td><span class="dbo-badge ' . $type_badge . '">' . esc_html($m['target_type']) . '</span></td>';
                echo '<td><code>' . esc_html($m['woo_target']) . '</code></td>';
                echo '<td>' . esc_html($m['transform']) . '</td>';
                echo '<td><code style="font-size:0.72rem;">' . esc_html($m['transform_cfg']) . '</code></td>';
                echo '<td><label class="fm-toggle"><input type="checkbox" ' . $enabled_checked . ' disabled /><span class="slider"></span></label></td>';
                echo '<td>';
                echo '<button class="fm-btn fm-btn-primary tigon-edit-mapping" data-id="' . $mid . '" style="padding:0.2rem 0.6rem;font-size:0.72rem;">Edit</button> ';
                echo '<button class="fm-btn tigon-delete-mapping" data-id="' . $mid . '" style="padding:0.2rem 0.6rem;font-size:0.72rem;background:#fee2e2;color:#991b1b;">Del</button>';
                echo '</td>';
                echo '</tr>';
            }
        }

        echo '</tbody></table></div>';

        // ── Add/Edit form ───────────────────────────────────────────
        echo '<h3 id="tigon-form-title" style="margin-top:1.5rem;">Add New Mapping</h3>';
        echo '<div class="fm-form">';
        echo '<input type="hidden" id="tigon-mapping-id" value="0" />';
        echo '<input type="hidden" id="tigon-sort-order" value="0" />';

        // DMS Field Path (with optgroups)
        echo '<div><label>DMS Field Path</label>';
        echo '<select id="tigon-dms-path"><option value="">-- Select DMS field --</option>';
        foreach ($field_groups as $gk => $fields) {
            $gl = $group_labels[$gk] ?? ucfirst($gk);
            echo '<optgroup label="' . esc_attr($gl) . '">';
            foreach ($fields as $f) {
                echo '<option value="' . esc_attr($f) . '">' . esc_html($f) . '</option>';
            }
            echo '</optgroup>';
        }
        echo '<option value="__custom__">Custom path...</option>';
        echo '</select>';
        echo '<input type="text" id="tigon-dms-path-custom" placeholder="e.g. myCustom.nested.field" style="display:none;margin-top:4px;" />';
        echo '</div>';

        // Target Type
        echo '<div><label>Target Type</label>';
        echo '<select id="tigon-target-type">';
        echo '<option value="postmeta">Post Meta</option>';
        echo '<option value="post">Post Field</option>';
        echo '<option value="taxonomy">Taxonomy</option>';
        echo '</select></div>';

        // WooCommerce Target (with optgroups per target type)
        echo '<div><label>WooCommerce Target</label>';
        echo '<select id="tigon-woo-target"><option value="">-- Select target --</option>';
        foreach ($woo_targets['postmeta'] as $t) {
            echo '<option value="' . esc_attr($t) . '">' . esc_html($t) . '</option>';
        }
        echo '<option value="__custom__">Custom key...</option>';
        echo '</select>';
        echo '<input type="text" id="tigon-woo-target-custom" placeholder="e.g. _my_custom_meta" style="display:none;margin-top:4px;" />';
        echo '</div>';

        // Transform
        echo '<div><label>Transform</label>';
        echo '<select id="tigon-transform">';
        foreach ($transforms as $key => $label) {
            echo '<option value="' . esc_attr($key) . '">' . $label . '</option>';
        }
        echo '</select></div>';

        // Transform Config (full width)
        echo '<div class="fm-span2"><label>Transform Config <small style="font-weight:400;color:#888;">(optional &mdash; used by prefix/suffix/template/boolean_label/static)</small></label>';
        echo '<input type="text" id="tigon-transform-cfg" placeholder="e.g. {value} Amp Hours, or [&quot;ELECTRIC&quot;,&quot;GAS&quot;]" />';
        echo '</div>';

        // Enabled + Actions row
        echo '<div style="display:flex;align-items:center;gap:0.5rem;">';
        echo '<label class="fm-toggle"><input type="checkbox" id="tigon-is-enabled" checked /><span class="slider"></span></label>';
        echo '<span style="font-weight:600;font-size:0.82rem;">Enabled</span>';
        echo '</div>';
        echo '<div class="fm-actions">';
        echo '<button class="fm-btn fm-btn-secondary" id="tigon-cancel-edit" style="display:none;">Cancel</button>';
        echo '<button class="fm-btn fm-btn-primary" id="tigon-save-mapping">Save Mapping</button>';
        echo '</div>';

        echo '</div>'; // end fm-form
        echo '</div>'; // end mappings panel

        // ═════════════════════════════════════════════════════════════
        // TAB 3: TARGET BROWSER
        // ═════════════════════════════════════════════════════════════
        echo '<div class="dbo-panel" data-panel="targets">';
        echo '<h2 style="margin-top:0;">WooCommerce Target Browser</h2>';
        echo '<p style="color:#666;font-size:0.85rem;">Browse all available WordPress/WooCommerce database targets that DMS feed fields can be mapped to. Targets marked "In Use" already have an active mapping.</p>';

        echo '<input type="text" class="dbo-search" id="target-search" placeholder="Search targets...">';

        // ── Post Meta targets ───────────────────────────────────────
        $postmeta_groups = [
            'WooCommerce Core' => [],
            'DMS Bridge'       => [],
            'Yoast SEO'        => [],
            'Google for WC'    => [],
            'Facebook for WC'  => [],
            'Pinterest for WC' => [],
        ];

        foreach ($woo_targets['postmeta'] as $t) {
            if (strpos($t, '_dms_') === 0 || $t === 'monroney_sticker') {
                $postmeta_groups['DMS Bridge'][] = $t;
            } elseif (strpos($t, '_yoast_') === 0) {
                $postmeta_groups['Yoast SEO'][] = $t;
            } elseif (strpos($t, '_wc_gla_') === 0) {
                $postmeta_groups['Google for WC'][] = $t;
            } elseif (strpos($t, '_wc_facebook') === 0 || strpos($t, '_wc_fb_') === 0) {
                $postmeta_groups['Facebook for WC'][] = $t;
            } elseif (strpos($t, '_wc_pinterest') === 0) {
                $postmeta_groups['Pinterest for WC'][] = $t;
            } else {
                $postmeta_groups['WooCommerce Core'][] = $t;
            }
        }

        // Post Meta sections
        foreach ($postmeta_groups as $pg_name => $pg_targets) {
            if (empty($pg_targets)) continue;
            $in_use = 0;
            foreach ($pg_targets as $t) { if (!empty($all_woo_flat[$t])) $in_use++; }
            $badge = 'blue';
            if ($pg_name === 'DMS Bridge') $badge = 'teal';
            elseif ($pg_name === 'Yoast SEO') $badge = 'green';
            elseif ($pg_name === 'Google for WC') $badge = 'orange';
            elseif ($pg_name === 'Facebook for WC') $badge = 'blue';
            elseif ($pg_name === 'Pinterest for WC') $badge = 'purple';

            $open = ($pg_name === 'WooCommerce Core' || $pg_name === 'DMS Bridge') ? ' open' : '';
            echo '<div class="dbo-section target-section"><div class="dbo-section-hd' . $open . '" onclick="this.classList.toggle(\'open\')">';
            echo '<span class="arrow">&#9654;</span>';
            echo '<h3>Post Meta: ' . esc_html($pg_name) . '</h3>';
            echo '<span class="dbo-badge ' . $badge . '">' . count($pg_targets) . '</span>';
            if ($in_use > 0) echo ' <span class="dbo-badge green">' . $in_use . ' in use</span>';
            echo '</div><div class="dbo-section-bd">';
            echo '<div class="dbo-scroll" style="max-height:300px;"><table class="dbo-table"><thead><tr><th>Meta Key</th><th>Status</th></tr></thead><tbody>';
            foreach ($pg_targets as $t) {
                $used = !empty($all_woo_flat[$t]);
                $badge_html = $used ? '<span class="dbo-badge green">In Use</span>' : '<span class="dbo-badge gray">Available</span>';
                echo '<tr class="target-row"><td><code>' . esc_html($t) . '</code></td><td>' . $badge_html . '</td></tr>';
            }
            echo '</tbody></table></div></div></div>';
        }

        // Post Field targets
        echo '<div class="dbo-section target-section"><div class="dbo-section-hd" onclick="this.classList.toggle(\'open\')">';
        echo '<span class="arrow">&#9654;</span><h3>Post Fields</h3>';
        echo '<span class="dbo-badge teal">' . count($woo_targets['post']) . '</span>';
        echo '</div><div class="dbo-section-bd">';
        echo '<div class="dbo-scroll" style="max-height:250px;"><table class="dbo-table"><thead><tr><th>Field</th><th>Status</th></tr></thead><tbody>';
        foreach ($woo_targets['post'] as $t) {
            $used = !empty($all_woo_flat[$t]);
            $badge_html = $used ? '<span class="dbo-badge green">In Use</span>' : '<span class="dbo-badge gray">Available</span>';
            echo '<tr class="target-row"><td><code>' . esc_html($t) . '</code></td><td>' . $badge_html . '</td></tr>';
        }
        echo '</tbody></table></div></div></div>';

        // Taxonomy targets
        echo '<div class="dbo-section target-section"><div class="dbo-section-hd" onclick="this.classList.toggle(\'open\')">';
        echo '<span class="arrow">&#9654;</span><h3>Taxonomies</h3>';
        echo '<span class="dbo-badge purple">' . count($woo_targets['taxonomy']) . '</span>';
        echo '</div><div class="dbo-section-bd">';
        echo '<div class="dbo-scroll" style="max-height:300px;"><table class="dbo-table"><thead><tr><th>Taxonomy</th><th>Status</th></tr></thead><tbody>';
        foreach ($woo_targets['taxonomy'] as $t) {
            $used = !empty($all_woo_flat[$t]);
            $badge_html = $used ? '<span class="dbo-badge green">In Use</span>' : '<span class="dbo-badge gray">Available</span>';
            echo '<tr class="target-row"><td><code>' . esc_html($t) . '</code></td><td>' . $badge_html . '</td></tr>';
        }
        echo '</tbody></table></div></div></div>';
        echo '</div>'; // end targets panel

        echo '</div>'; // end dbo-wrap

        // ═════════════════════════════════════════════════════════════
        // JAVASCRIPT
        // ═════════════════════════════════════════════════════════════
        echo '<script>
        (function($) {
            var nonce = ' . wp_json_encode($nonce) . ';
            var wooTargets = ' . wp_json_encode($woo_targets) . ';

            /* ── Tab switching ──────────────────────────────────────── */
            var tabs = document.querySelectorAll(".dbo-tab");
            var panels = document.querySelectorAll(".dbo-panel");
            tabs.forEach(function(tab) {
                tab.addEventListener("click", function() {
                    tabs.forEach(function(t) { t.classList.remove("active"); });
                    panels.forEach(function(p) { p.classList.remove("active"); });
                    tab.classList.add("active");
                    var panel = document.querySelector("[data-panel=\"" + tab.dataset.tab + "\"]");
                    if (panel) panel.classList.add("active");
                });
            });

            /* ── Feed Explorer: search ──────────────────────────────── */
            document.getElementById("feed-search").addEventListener("input", function() {
                var q = this.value.toLowerCase();
                document.querySelectorAll(".feed-row").forEach(function(row) {
                    row.style.display = row.textContent.toLowerCase().indexOf(q) > -1 ? "" : "none";
                });
                // Show sections that have visible rows
                document.querySelectorAll(".feed-section").forEach(function(sec) {
                    var visible = sec.querySelectorAll(".feed-row:not([style*=\"display: none\"])");
                    sec.style.display = (!q || visible.length > 0) ? "" : "none";
                    if (q && visible.length > 0) sec.querySelector(".dbo-section-hd").classList.add("open");
                });
            });

            /* ── Feed Explorer: filter pills ────────────────────────── */
            document.querySelectorAll("#feed-filter-pills .dbo-pill").forEach(function(pill) {
                pill.addEventListener("click", function() {
                    document.querySelectorAll("#feed-filter-pills .dbo-pill").forEach(function(p) { p.classList.remove("active"); });
                    pill.classList.add("active");
                    var filter = pill.dataset.feedFilter;
                    document.querySelectorAll(".feed-row").forEach(function(row) {
                        if (filter === "all") { row.style.display = ""; }
                        else if (filter === "mapped") { row.style.display = row.dataset.mapped === "1" ? "" : "none"; }
                        else if (filter === "unmapped") { row.style.display = row.dataset.mapped === "0" ? "" : "none"; }
                    });
                    document.querySelectorAll(".feed-section").forEach(function(sec) {
                        var visible = sec.querySelectorAll(".feed-row:not([style*=\"display: none\"])");
                        sec.style.display = visible.length > 0 ? "" : "none";
                    });
                });
            });

            /* ── Mapping Editor: search ─────────────────────────────── */
            document.getElementById("mapping-search").addEventListener("input", function() {
                var q = this.value.toLowerCase();
                document.querySelectorAll("#tigon-mapping-rows tr").forEach(function(row) {
                    row.style.display = row.textContent.toLowerCase().indexOf(q) > -1 ? "" : "none";
                });
            });

            /* ── Target Browser: search ─────────────────────────────── */
            document.getElementById("target-search").addEventListener("input", function() {
                var q = this.value.toLowerCase();
                document.querySelectorAll(".target-row").forEach(function(row) {
                    row.style.display = row.textContent.toLowerCase().indexOf(q) > -1 ? "" : "none";
                });
                document.querySelectorAll(".target-section").forEach(function(sec) {
                    var visible = sec.querySelectorAll(".target-row:not([style*=\"display: none\"])");
                    sec.style.display = (!q || visible.length > 0) ? "" : "none";
                    if (q && visible.length > 0) sec.querySelector(".dbo-section-hd").classList.add("open");
                });
            });

            /* ── Quick Map button (from Feed Explorer) ──────────────── */
            window.tigonFM = {
                quickMap: function(dmsPath) {
                    // Switch to Mapping Editor tab
                    tabs.forEach(function(t) { t.classList.remove("active"); });
                    panels.forEach(function(p) { p.classList.remove("active"); });
                    var mapTab = document.querySelector("[data-tab=\"mappings\"]");
                    var mapPanel = document.querySelector("[data-panel=\"mappings\"]");
                    if (mapTab) mapTab.classList.add("active");
                    if (mapPanel) mapPanel.classList.add("active");

                    // Pre-fill DMS path
                    var $sel = $("#tigon-dms-path");
                    if ($sel.find("option[value=\'" + dmsPath + "\']").length) {
                        $sel.val(dmsPath);
                        $("#tigon-dms-path-custom").hide();
                    } else {
                        $sel.val("__custom__");
                        $("#tigon-dms-path-custom").show().val(dmsPath);
                    }

                    // Reset form state
                    $("#tigon-mapping-id").val(0);
                    $("#tigon-form-title").text("Add New Mapping");
                    $("#tigon-cancel-edit").hide();

                    // Scroll to form
                    $("html, body").animate({ scrollTop: $("#tigon-form-title").offset().top - 50 }, 300);
                }
            };

            /* ── Form: toggle custom inputs ─────────────────────────── */
            $("#tigon-dms-path").on("change", function() {
                $("#tigon-dms-path-custom").toggle($(this).val() === "__custom__");
            });
            $("#tigon-woo-target").on("change", function() {
                $("#tigon-woo-target-custom").toggle($(this).val() === "__custom__");
            });

            /* ── Form: update targets by type ───────────────────────── */
            $("#tigon-target-type").on("change", function() {
                var type = $(this).val();
                var targets = wooTargets[type] || [];
                var $select = $("#tigon-woo-target");
                $select.empty().append(\'<option value="">-- Select target --</option>\');
                targets.forEach(function(t) {
                    $select.append(\'<option value="\' + t + \'">\' + t + \'</option>\');
                });
                $select.append(\'<option value="__custom__">Custom key...</option>\');
                $("#tigon-woo-target-custom").hide().val("");
            });

            /* ── Inline status flash (replaces page reload) ───────── */
            function showStatus(msg, isError) {
                var $el = $("#tigon-mapping-status");
                if (!$el.length) {
                    $el = $("<div id=\"tigon-mapping-status\"></div>").insertAfter("#tigon-form-title");
                }
                $el.text(msg)
                   .css({
                       display: "block", padding: "0.6rem 1rem", borderRadius: "4px", fontWeight: 600, marginBottom: "0.7rem",
                       background: isError ? "#fee2e2" : "#d1fae5",
                       color:      isError ? "#991b1b" : "#065f46",
                       border:     isError ? "1px solid #fca5a5" : "1px solid #6ee7b7"
                   });
                if (!isError) setTimeout(function() { $el.fadeOut(400); }, 3000);
            }

            /* ── Save mapping ───────────────────────────────────────── */
            $("#tigon-save-mapping").on("click", function() {
                var $btn = $(this);
                var dmsPath = $("#tigon-dms-path").val();
                if (dmsPath === "__custom__") dmsPath = $("#tigon-dms-path-custom").val().trim();
                var wooTarget = $("#tigon-woo-target").val();
                if (wooTarget === "__custom__") wooTarget = $("#tigon-woo-target-custom").val().trim();

                if (!dmsPath || !wooTarget) {
                    showStatus("Please select both a DMS field and a WooCommerce target.", true);
                    return;
                }

                $btn.prop("disabled", true).text("Saving...");

                var data = {
                    action: "tigon_dms_save_field_mapping",
                    nonce: nonce,
                    mapping_id: $("#tigon-mapping-id").val(),
                    dms_path: dmsPath,
                    woo_target: wooTarget,
                    target_type: $("#tigon-target-type").val(),
                    transform: $("#tigon-transform").val(),
                    transform_cfg: $("#tigon-transform-cfg").val(),
                    is_enabled: $("#tigon-is-enabled").is(":checked") ? 1 : 0,
                    sort_order: parseInt($("#tigon-sort-order").val()) || 0
                };

                $.post(globals.ajaxurl, data, function(resp) {
                    if (resp.success) {
                        // Reload to reflect the change in the table
                        location.reload();
                    } else {
                        showStatus("Save failed: " + (resp.data || "Unknown error"), true);
                        $btn.prop("disabled", false).text("Save Mapping");
                    }
                }).fail(function(xhr) {
                    showStatus("Network error (HTTP " + xhr.status + "). Your session may have expired — try refreshing the page.", true);
                    $btn.prop("disabled", false).text("Save Mapping");
                });
            });

            /* ── Delete mapping ─────────────────────────────────────── */
            $(document).on("click", ".tigon-delete-mapping", function() {
                if (!confirm("Delete this mapping?")) return;
                var $btn = $(this);
                var id = $btn.data("id");
                $btn.prop("disabled", true).text("...");
                $.post(globals.ajaxurl, {
                    action: "tigon_dms_delete_field_mapping",
                    nonce: nonce,
                    mapping_id: id
                }, function(resp) {
                    if (resp.success) {
                        $btn.closest("tr").fadeOut(300, function() { $(this).remove(); });
                    } else {
                        showStatus("Delete failed: " + (resp.data || "Unknown error"), true);
                        $btn.prop("disabled", false).text("Del");
                    }
                }).fail(function(xhr) {
                    showStatus("Network error (HTTP " + xhr.status + "). Try refreshing the page.", true);
                    $btn.prop("disabled", false).text("Del");
                });
            });

            /* ── Edit mapping — populate form from data attributes ──── */
            $(document).on("click", ".tigon-edit-mapping", function() {
                var $row = $(this).closest("tr");
                var id         = $row.data("mapping-id");
                var dmsPath    = $row.data("dms-path")    || "";
                var wooTarget  = $row.data("woo-target")  || "";
                var targetType = $row.data("target-type")  || "postmeta";
                var transform  = $row.data("transform")    || "direct";
                var cfg        = $row.data("transform-cfg") || "";
                var enabled    = $row.data("is-enabled") == 1;
                var sortOrder  = $row.data("sort-order")   || 0;

                // Convert to strings (jQuery may parse numbers/booleans)
                dmsPath   = String(dmsPath);
                wooTarget = String(wooTarget);
                cfg       = String(cfg);

                $("#tigon-mapping-id").val(id);
                $("#tigon-form-title").text("Edit Mapping #" + id);
                $("#tigon-cancel-edit").show();

                // Set target type first — this rebuilds the WooCommerce target dropdown
                $("#tigon-target-type").val(targetType).trigger("change");

                // Set DMS path (use custom input if not in the dropdown)
                if ($("#tigon-dms-path option[value=\'" + dmsPath + "\']").length) {
                    $("#tigon-dms-path").val(dmsPath);
                    $("#tigon-dms-path-custom").hide();
                } else {
                    $("#tigon-dms-path").val("__custom__");
                    $("#tigon-dms-path-custom").show().val(dmsPath);
                }

                // Set WooCommerce target after dropdown rebuild settles
                setTimeout(function() {
                    if ($("#tigon-woo-target option[value=\'" + wooTarget + "\']").length) {
                        $("#tigon-woo-target").val(wooTarget);
                        $("#tigon-woo-target-custom").hide();
                    } else {
                        $("#tigon-woo-target").val("__custom__");
                        $("#tigon-woo-target-custom").show().val(wooTarget);
                    }
                }, 80);

                $("#tigon-transform").val(transform);
                $("#tigon-transform-cfg").val(cfg);
                $("#tigon-is-enabled").prop("checked", enabled);
                $("#tigon-sort-order").val(sortOrder);

                $("html, body").animate({ scrollTop: $("#tigon-form-title").offset().top - 50 }, 300);
            });

            /* ── Cancel edit — reset form ───────────────────────────── */
            $("#tigon-cancel-edit").on("click", function() {
                $("#tigon-mapping-id").val(0);
                $("#tigon-form-title").text("Add New Mapping");
                $(this).hide();
                $("#tigon-mapping-status").hide();
                $("#tigon-dms-path").val("");
                $("#tigon-dms-path-custom").hide().val("");
                $("#tigon-woo-target").val("");
                $("#tigon-woo-target-custom").hide().val("");
                $("#tigon-target-type").val("postmeta").trigger("change");
                $("#tigon-transform").val("direct");
                $("#tigon-transform-cfg").val("");
                $("#tigon-is-enabled").prop("checked", true);
                $("#tigon-sort-order").val(0);
            });
        })(jQuery);
        </script>
        ';
    }

    public static function settings_page()
    {
        $nonce = wp_create_nonce("tigon_dms_run_import_nonce");
        $link = admin_url('admin-ajax.php?action=tigon_dms_save_settings&nonce=' . $nonce);

        $dms_url = 'dms.com';
        $api_key = 'key';
        $file_source = 'cdn.com';

        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $table_name = $wpdb->prefix . 'tigon_dms_config';

        $github_token = $wpdb->get_var("SELECT option_value FROM $table_name WHERE option_name = 'github_token'") ?? 'e.g. github_pat_...';
        $github_token = substr($github_token, 0, 16) . '•••••••••••••••' . substr($github_token, -5);

        $dms_url = $wpdb->get_var("SELECT option_value FROM $table_name WHERE option_name = 'dms_url'") ?? 'e.g. https://api.your-dms.com';

        $api_key = $wpdb->get_var("SELECT option_value FROM $table_name WHERE option_name = 'user_token'") ?? 'e.g. 00000000-0000-0000-000000000000';
        $shown_key = substr($api_key, -6);
        $api_key = substr_replace(preg_replace('/[^-]/', '•', $api_key), $shown_key, -6, 6);
        
        $file_source = $wpdb->get_var("SELECT option_value FROM $table_name WHERE option_name = 'file_source'") ?? 'e.g. https://s3.amazonaws.com/your.bucket.s3';

        self::page_header();

        // Textbox placeholders from database
        $name = '{^make}® {^model} {cartColor} in {city}, {stateAbbr}';
        $slug = '{make}-{model}-{cartColor}-seat-{seatColor}-{city}-{state}';
        $image_name = '{^make}® {^model} {cartColor} in {city}, {stateAbbr} image';
        $monroney_name = '{^make}® {^model} {cartColor} in {city}, {stateAbbr} monroney';
        $description = 'Lorem ipsum dolor sit amet';
        $short_description = 'Lorem ipsum';

        echo '
        <div class="body">
            <div class="tabbed-panel">
                <div class="tigon-dms-nav">
                    <button class="tigon-dms-tab" id="general-tab">General</button>
                    <button class="tigon-dms-tab" id="urls-tab">DMS API &amp; S3</button>
                    <button class="tigon-dms-tab" id="endpoints-tab">REST Endpoints</button>
                    <button class="tigon-dms-tab" id="schema-tab">Schema</button>
                </div>

                <div class="action-box settings-panel" id="general">
                    <div class="settings-panel-header">
                        <h3>General Configuration</h3>
                        <p>Configure your DMS API connection and authentication credentials.</p>
                    </div>
                    <div class="settings form">
                        <div>
                            <span>GitHub Access Token:</span>
                            <input type="text" style="float:right" id="txt-github-token" placeholder="' . $github_token . '" />
                        </div>
                        <div>
                            <span>DMS API Address:</span>
                            <input type="text" style="float:right" id="txt-url" placeholder="' . $dms_url . '" />
                        </div>
                        <div>
                            <span>DMS Amplify User ID:</span>
                            <input type="text" style="float:right" id="txt-api-key" placeholder="' . $api_key . '"></textarea>
                        </div>
                        <div>
                            <span>File source:</span>
                            <input type="text" style="float:right" id="txt-file-source" placeholder="' . $file_source . '"></textarea>
                        </div>
                    </div>
                    <a class="tigon_dms_action tigon_dms_save" data-nonce="' . $nonce . '"><button>Save Settings</button></a>
                </div>

                <div class="action-box settings-panel" id="urls">
                    <div class="settings-panel-header">
                        <h3>DMS API &amp; S3 URLs</h3>
                        <p>Production API endpoints and S3 bucket prefixes used by this plugin to fetch inventory data and assets.</p>
                    </div>
                    <div class="settings-panel-body">
                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                            <span style="min-width:200px; font-weight:600; font-size:0.85rem;">API Base URL:</span>
                            <code id="url-api-base" style="background:#f0f4f8; padding:0.35rem 0.7rem; border-radius:4px; font-size:0.82rem; word-break:break-all; flex:1; border:1px solid #d0d5dd;">https://api.tigondms.com/wp-website</code>
                            <button type="button" class="tigon-copy-btn" data-target="url-api-base" style="padding:0.3rem 0.7rem; font-size:0.78rem; cursor:pointer; border:none; border-radius:4px; background-color:#557486; color:#F4F4F4;">Copy</button>
                        </div>
                        <div style="font-size:0.78rem; color:#888; margin-left:200px; margin-top:-0.2rem;">
                            Base URL for all outbound DMS API requests.
                        </div>

                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-top:0.7rem;">
                            <span style="min-width:200px; font-weight:600; font-size:0.85rem;">Active Inventory Carts:</span>
                            <code id="url-get-carts" style="background:#f0f4f8; padding:0.35rem 0.7rem; border-radius:4px; font-size:0.82rem; word-break:break-all; flex:1; border:1px solid #d0d5dd;">https://api.tigondms.com/wp-website/get-carts</code>
                            <button type="button" class="tigon-copy-btn" data-target="url-get-carts" style="padding:0.3rem 0.7rem; font-size:0.78rem; cursor:pointer; border:none; border-radius:4px; background-color:#557486; color:#F4F4F4;">Copy</button>
                        </div>
                        <div style="font-size:0.78rem; color:#888; margin-left:200px; margin-top:-0.2rem;">
                            <strong>POST</strong> &mdash; Fetch paginated active inventory carts from the DMS.
                        </div>

                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-top:0.7rem;">
                            <span style="min-width:200px; font-weight:600; font-size:0.85rem;">Tigon Stores:</span>
                            <code id="url-tigon-stores" style="background:#f0f4f8; padding:0.35rem 0.7rem; border-radius:4px; font-size:0.82rem; word-break:break-all; flex:1; border:1px solid #d0d5dd;">https://api.tigondms.com/wp-website/tigon-stores</code>
                            <button type="button" class="tigon-copy-btn" data-target="url-tigon-stores" style="padding:0.3rem 0.7rem; font-size:0.78rem; cursor:pointer; border:none; border-radius:4px; background-color:#557486; color:#F4F4F4;">Copy</button>
                        </div>
                        <div style="font-size:0.78rem; color:#888; margin-left:200px; margin-top:-0.2rem;">
                            <strong>GET</strong> &mdash; Retrieve list of Tigon store locations for inventory filtering.
                        </div>

                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-top:0.7rem;">
                            <span style="min-width:200px; font-weight:600; font-size:0.85rem;">Monroney Sticker Images:</span>
                            <code id="url-monroney" style="background:#f0f4f8; padding:0.35rem 0.7rem; border-radius:4px; font-size:0.82rem; word-break:break-all; flex:1; border:1px solid #d0d5dd;">https://s3.amazonaws.com/prod.docs.s3/cart-window-stickers/</code>
                            <button type="button" class="tigon-copy-btn" data-target="url-monroney" style="padding:0.3rem 0.7rem; font-size:0.78rem; cursor:pointer; border:none; border-radius:4px; background-color:#557486; color:#F4F4F4;">Copy</button>
                        </div>
                        <div style="font-size:0.78rem; color:#888; margin-left:200px; margin-top:-0.2rem;">
                            S3 prefix for window sticker (Monroney) PDF/image files.
                        </div>

                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-top:0.7rem;">
                            <span style="min-width:200px; font-weight:600; font-size:0.85rem;">Website Cart Images:</span>
                            <code id="url-cart-images" style="background:#f0f4f8; padding:0.35rem 0.7rem; border-radius:4px; font-size:0.82rem; word-break:break-all; flex:1; border:1px solid #d0d5dd;">https://s3.amazonaws.com/test.docs.s3/carts/</code>
                            <button type="button" class="tigon-copy-btn" data-target="url-cart-images" style="padding:0.3rem 0.7rem; font-size:0.78rem; cursor:pointer; border:none; border-radius:4px; background-color:#557486; color:#F4F4F4;">Copy</button>
                        </div>
                        <div style="font-size:0.78rem; color:#888; margin-left:200px; margin-top:-0.2rem;">
                            S3 prefix for cart product images displayed on the website.
                        </div>
                    </div>
                </div>

                <div class="action-box settings-panel" id="endpoints">
                    <div class="settings-panel-header">
                        <h3>REST API Endpoints</h3>
                        <p>These are the endpoint addresses the DMS uses to push data to this site. All endpoints require authentication (WordPress application password or logged-in admin session).</p>
                    </div>
                    <div class="settings-panel-body">
                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap;">
                            <span style="min-width:160px; font-weight:600; font-size:0.85rem;">Single Cart Push:</span>
                            <code id="endpoint-push" style="background:#f0f4f8; padding:0.35rem 0.7rem; border-radius:4px; font-size:0.82rem; word-break:break-all; flex:1; border:1px solid #d0d5dd;">' . esc_html(rest_url('tigon-dms-connect/v1/push')) . '</code>
                            <button type="button" class="tigon-copy-btn" data-target="endpoint-push" style="padding:0.3rem 0.7rem; font-size:0.78rem; cursor:pointer; border:none; border-radius:4px; background-color:#557486; color:#F4F4F4;">Copy</button>
                        </div>
                        <div style="font-size:0.78rem; color:#888; margin-left:160px; margin-top:-0.2rem;">
                            <strong>POST</strong> &mdash; Send a single DMS cart JSON object when it is updated or changed. Creates or updates the WooCommerce product using field mappings and schema templates.
                        </div>

                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-top:0.7rem;">
                            <span style="min-width:160px; font-weight:600; font-size:0.85rem;">Push Used Cart:</span>
                            <code id="endpoint-used" style="background:#f0f4f8; padding:0.35rem 0.7rem; border-radius:4px; font-size:0.82rem; word-break:break-all; flex:1; border:1px solid #d0d5dd;">' . esc_html(rest_url('tigon-dms-connect/used')) . '</code>
                            <button type="button" class="tigon-copy-btn" data-target="endpoint-used" style="padding:0.3rem 0.7rem; font-size:0.78rem; cursor:pointer; border:none; border-radius:4px; background-color:#557486; color:#F4F4F4;">Copy</button>
                        </div>
                        <div style="font-size:0.78rem; color:#888; margin-left:160px; margin-top:-0.2rem;">
                            <strong>POST</strong> &mdash; Create or update a used cart. <strong>DELETE</strong> &mdash; Remove a used cart.
                        </div>

                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-top:0.7rem;">
                            <span style="min-width:160px; font-weight:600; font-size:0.85rem;">Push New Cart:</span>
                            <code id="endpoint-new" style="background:#f0f4f8; padding:0.35rem 0.7rem; border-radius:4px; font-size:0.82rem; word-break:break-all; flex:1; border:1px solid #d0d5dd;">' . esc_html(rest_url('tigon-dms-connect/new/update')) . '</code>
                            <button type="button" class="tigon-copy-btn" data-target="endpoint-new" style="padding:0.3rem 0.7rem; font-size:0.78rem; cursor:pointer; border:none; border-radius:4px; background-color:#557486; color:#F4F4F4;">Copy</button>
                        </div>
                        <div style="font-size:0.78rem; color:#888; margin-left:160px; margin-top:-0.2rem;">
                            <strong>POST</strong> &mdash; Create or update a new (non-used) cart.
                        </div>

                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-top:0.7rem;">
                            <span style="min-width:160px; font-weight:600; font-size:0.85rem;">Lookup by Slug:</span>
                            <code id="endpoint-pid" style="background:#f0f4f8; padding:0.35rem 0.7rem; border-radius:4px; font-size:0.82rem; word-break:break-all; flex:1; border:1px solid #d0d5dd;">' . esc_html(rest_url('tigon-dms-connect/new/pid')) . '</code>
                            <button type="button" class="tigon-copy-btn" data-target="endpoint-pid" style="padding:0.3rem 0.7rem; font-size:0.78rem; cursor:pointer; border:none; border-radius:4px; background-color:#557486; color:#F4F4F4;">Copy</button>
                        </div>
                        <div style="font-size:0.78rem; color:#888; margin-left:160px; margin-top:-0.2rem;">
                            <strong>POST</strong> &mdash; Get WooCommerce product ID by website URL slug.
                        </div>

                        <div style="display:flex; align-items:center; gap:0.5rem; flex-wrap:wrap; margin-top:0.7rem;">
                            <span style="min-width:160px; font-weight:600; font-size:0.85rem;">Showcase Grid:</span>
                            <code id="endpoint-showcase" style="background:#f0f4f8; padding:0.35rem 0.7rem; border-radius:4px; font-size:0.82rem; word-break:break-all; flex:1; border:1px solid #d0d5dd;">' . esc_html(rest_url('tigon-dms-connect/showcase')) . '</code>
                            <button type="button" class="tigon-copy-btn" data-target="endpoint-showcase" style="padding:0.3rem 0.7rem; font-size:0.78rem; cursor:pointer; border:none; border-radius:4px; background-color:#557486; color:#F4F4F4;">Copy</button>
                        </div>
                        <div style="font-size:0.78rem; color:#888; margin-left:160px; margin-top:-0.2rem;">
                            <strong>POST</strong> &mdash; Set the featured product grid for a location page.
                        </div>
                    </div>
                </div>

                <div class="action-box settings-panel" id="schema">
                    <div class="settings-panel-header">
                        <h3>Full Payload Schema</h3>
                        <p>Fetches active inventory from the DMS API (<code>pageNumber: 0, pageSize: 20</code>) and merges all fields into one unified schema showing every key, its type, and all observed values.</p>
                    </div>

                    <div style="display:flex; align-items:center; gap:1rem; margin-bottom:1.5rem;">
                        <button type="button" id="fetch-schema-btn">Fetch Schema</button>
                        <span id="schema-status" style="font-size:0.85rem; color:#666;"></span>
                    </div>

                    <div id="schema-output">
                        <p style="color:#888; text-align:center; padding:2rem 0;">Click <strong>Fetch Schema</strong> to load the full payload schema from the DMS API.</p>
                    </div>

                    <hr style="margin:2.5rem 0; border:none; border-top:1px solid #ddd;" />

                    <h3>Schema Templates</h3>
                    <div class="drag-n-drop">
                        <div class="settings form" id="attr-form">
                            <div>
                                <span>Name Schema:</span>
                                <input type="text" style="float:right" id="Name Schema" placeholder="'.$name.'" />
                            </div>
                            
                            <div>
                                <span>Slug Schema:</span>
                                <input type="text" style="float:right" id="Slug Schema" placeholder="'.$slug.'" />
                            </div>
                            
                            <div>
                                <span>Image Name:</span>
                                <input type="text" style="float:right" id="Image Name" placeholder="'.$image_name.'" />
                            </div>
                            
                            <div>
                                <span>Monroney Name:</span>
                                <input type="text" style="float:right" id="Monroney Name" placeholder="'.$monroney_name.'" />
                            </div>
                            
                            <div>
                                <span>Description:</span>
                                <input type="text" style="float:right" id="Description" placeholder="'.$description.'" />
                            </div>
                            
                            <div>
                                <span>Short Description:</span>
                                <input type="text" style="float:right" id="Short Description" placeholder="'.$short_description.'" />
                            </div>
                            
                            <!--<div>
                                <span>Field Overrides:</span>
                                <input type="text" style="float:right" id="Field Overrides" placeholder="'.$dms_url.'" />
                            </div>-->

                            <div>
                                <span class="caret">Catgories:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Attributes:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Taxonomies:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div>
                                <span class="caret">Product Addons:</span>
                            </div>
                            
                            <div style="flex-direction:column;">
                                <span class="caret" style="align-self:flex-start">Custom Tabs:</span>
                                <span class="nested">
                                    <div>
                                        <span>Short Description:</span>
                                        <input type="text" style="float:right" id="Short Description" placeholder="'.$dms_url.'" />
                                    </div>
                                    <div>
                                        <span>Short Description:</span>
                                        <input type="text" style="float:right" id="Short Description" placeholder="'.$dms_url.'" />
                                    </div>
                                </span>
                            </div>
                        </div>
                        <div id="dms-schema">
                            loading dms properties...
                        </div>
                    </div>
                    <a id="save" class="tigon_dms_action tigon_dms_schema_save" data-nonce="' . $nonce . '"><button>Save Settings</button></a>
                </div>
            </div>
                </div>
        </div>
        <script>
        document.querySelectorAll(".tigon-copy-btn").forEach(function(btn){
            btn.addEventListener("click", function(){
                var target = document.getElementById(btn.dataset.target);
                if(!target) return;
                var text = target.textContent;
                if(navigator.clipboard && navigator.clipboard.writeText){
                    navigator.clipboard.writeText(text).then(function(){
                        btn.textContent = "Copied!";
                        setTimeout(function(){ btn.textContent = "Copy"; }, 2000);
                    });
                } else {
                    var ta = document.createElement("textarea");
                    ta.value = text;
                    ta.style.position = "fixed";
                    ta.style.opacity = "0";
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand("copy");
                    document.body.removeChild(ta);
                    btn.textContent = "Copied!";
                    setTimeout(function(){ btn.textContent = "Copy"; }, 2000);
                }
            });
        });
        </script>
        ';
    }

    private static function checkboxes(): string
    {
        $product_fields = Product_Fields::get_options();
        $component = '
            <div class="import-fields-head">
                <div class="cb-container top-level" style="margin-right:-3.4rem; flex-grow:0;">
                    <input type="checkbox"/>
                    All
                </div>
                <h2 style="margin:auto;">Force Overwrite Data</h2>
                <span class="caret" style="margin-left:-1rem"></span>
            </div>
            <div class="checkbox-list hidden">
        ';
        foreach ($product_fields as $field) {
            $component = $component .
                '<div class="cb-container import-field" constant="' .
                strtoupper($field) .
                '"><input type="checkbox" /> ' .
                ucwords(str_replace('_', ' ', $field)) .
                '</div>';
        }
        $component = $component . '</div>';

        return $component;
    }
}
