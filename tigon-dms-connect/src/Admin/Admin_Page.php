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
                <div class="tigon-dms-nav" style="flex-direction:row;">
                    <button class="tigon-dms-tab" id="general-tab">General</button>
                    <button class="tigon-dms-tab" id="schema-tab">Schema</button>
                </div>

                <div class="action-box" id="general">
                    <h3>General Configuration</h3>
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
                    <a id="save" class="tigon_dms_action tigon_dms_save" data-nonce="' . $nonce . '"><button>Save Settings</button></a>

                    <hr style="margin:2.5rem 0; border:none; border-top:1px solid #ddd;" />

                    <h3 style="margin:0 0 0.5rem 0;">DMS API &amp; S3 URLs</h3>
                    <p style="font-size:0.85rem; color:#666; margin:0 0 1rem 0;">Production API endpoints and S3 bucket prefixes used by this plugin to fetch inventory data and assets.</p>

                    <div class="settings form" style="gap:0.6rem;">
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

                <div class="action-box" id="schema">
                    <h3>Full Payload Schema</h3>
                    <p style="font-size:0.85rem; color:#666; margin:0 0 1rem 0;">
                        Fetches active inventory from the DMS API (<code style="background:#f0f4f8; padding:0.15rem 0.4rem; border-radius:3px; font-size:0.8rem;">pageNumber: 0, pageSize: 20</code>) and merges all fields into one unified schema showing every key, its type, and all observed values.
                    </p>

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
