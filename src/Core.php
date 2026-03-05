<?php

namespace Tigon\DmsConnect;

class Core
{

    private function __construct()
    {
    }

    /**
     * Get the base URL for admin assets
     * @return string
     */
    public static function asset_url()
    {
        return TIGON_DMS_PLUGIN_URL . 'assets/js/tigon-dms/';
    }

    /**
     * Get the base CSS URL
     * @return string
     */
    public static function css_url()
    {
        return TIGON_DMS_PLUGIN_URL . 'assets/css/';
    }

    public static function init()
    {
        // Enqueue scripts
        add_action('load-toplevel_page_tigon-dms-connect', 'Tigon\DmsConnect\Core::diagnostic_script_enqueue');
        add_action('load-tigon-dms-connect_page_settings', 'Tigon\DmsConnect\Core::settings_script_enqueue');
        add_action('load-tigon-dms-connect_page_field-mapping', 'Tigon\DmsConnect\Core::field_mapping_script_enqueue');
        add_action('load-tigon-dms-connect_page_dms-inventory-sync', 'Tigon\DmsConnect\Core::sync_script_enqueue');

        // Register Ajax functions
        add_action('wp_ajax_tigon_dms_save_settings', 'Tigon\DmsConnect\Admin\Ajax_Settings_Controller::save_settings');
        add_action('wp_ajax_tigon_dms_get_dms_props', 'Tigon\DmsConnect\Admin\Ajax_Settings_Controller::get_dms_props');
        add_action('wp_ajax_tigon_dms_get_full_schema', 'Tigon\DmsConnect\Admin\Ajax_Settings_Controller::get_full_schema');
        add_action('wp_ajax_tigon_dms_sync_mapped', 'Tigon\DmsConnect\Core::ajax_sync_mapped_inventory');
        add_action('wp_ajax_tigon_dms_sync_selective', 'Tigon\DmsConnect\Core::ajax_sync_selective');

        // Batched sync handlers (timeout-safe chunked processing)
        add_action('wp_ajax_tigon_dms_sync_selective_init', 'Tigon\DmsConnect\Core::ajax_sync_selective_init');
        add_action('wp_ajax_tigon_dms_sync_selective_batch', 'Tigon\DmsConnect\Core::ajax_sync_selective_batch');
        add_action('wp_ajax_tigon_dms_sync_mapped_init', 'Tigon\DmsConnect\Core::ajax_sync_mapped_init');
        add_action('wp_ajax_tigon_dms_sync_mapped_batch', 'Tigon\DmsConnect\Core::ajax_sync_mapped_batch');
        add_action('wp_ajax_tigon_dms_publish_synced_init', 'Tigon\DmsConnect\Core::ajax_publish_synced_init');
        add_action('wp_ajax_tigon_dms_publish_synced_batch', 'Tigon\DmsConnect\Core::ajax_publish_synced_batch');

        // Field mapping AJAX handlers
        add_action('wp_ajax_tigon_dms_get_field_mappings', 'Tigon\DmsConnect\Core::ajax_get_field_mappings');
        add_action('wp_ajax_tigon_dms_save_field_mapping', 'Tigon\DmsConnect\Core::ajax_save_field_mapping');
        add_action('wp_ajax_tigon_dms_delete_field_mapping', 'Tigon\DmsConnect\Core::ajax_delete_field_mapping');
        add_action('wp_ajax_tigon_dms_get_mapping_meta', 'Tigon\DmsConnect\Core::ajax_get_mapping_meta');
        add_action('wp_ajax_tigon_dms_save_locations', 'Tigon\DmsConnect\Admin\Ajax_Settings_Controller::save_locations');

        // Add admin page
        add_action('admin_menu', 'Tigon\DmsConnect\Admin\Admin_Page::add_menu_page');

        // Late hook: nuke the legacy Import submenu no matter who registers it
        add_action('admin_menu', function () {
            remove_submenu_page('tigon-dms-connect', 'import');
        }, 999);

        // Ensure custom DB tables exist on every admin load.
        // register_activation_hook does NOT fire on plugin updates,
        // so we check a stored schema version and run install if it
        // is missing or outdated.
        add_action('admin_init', 'Tigon\DmsConnect\Core::maybe_upgrade_db');

        // Allow for automatic taxonomy updates
        add_action('woocommerce_rest_insert_product_object', 'Tigon\DmsConnect\Core::update_taxonomy', 10, 3);

        // Register REST routes
        add_action('rest_api_init', 'Tigon\DmsConnect\Core::register_rest_routes');

        // Plugin Lifecycle Hooks - use the main DMS Bridge plugin file
        register_activation_hook(TIGON_DMS_PLUGIN_DIR . 'dms-bridge-plugin.php', 'Tigon\DmsConnect\Core::install');

        // Auto update through github
        if (is_admin()) {
            global $wpdb;
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            $table_name = $wpdb->prefix . 'tigon_dms_config';
            // Only load updater if config table exists
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
                $config = array(
                    'slug' => basename(TIGON_DMS_PLUGIN_DIR) . '/dms-bridge-plugin.php',
                    'proper_folder_name' => basename(TIGON_DMS_PLUGIN_DIR),
                    'api_url' => 'https://api.github.com/repos/TigonGolfCarts/wordpress_connection',
                    'raw_url' => 'https://raw.github.com/TigonGolfCarts/wordpress_connection/main',
                    'github_url' => 'https://github.com/TigonGolfCarts/wordpress_connection',
                    'zip_url' => 'https://github.com/TigonGolfCarts/wordpress_connection/zipball/main',
                    'sslverify' => true,
                    'requires' => '3.0',
                    'tested' => '3.3',
                    'readme' => 'README.md',
                    'access_token' => $wpdb->get_var('SELECT option_value FROM ' . $table_name . ' WHERE option_name = "github_token"'),
                );
                new \Tigon\DmsConnect\Includes\WP_GitHub_Updater($config);
            }
        }

        // Add archive extension hooks
        add_action('pre_get_posts', 'Tigon\DmsConnect\Includes\Product_Archive_Extension::custom_order_products', 999999);
        add_action('wp', 'Tigon\DmsConnect\Core::remove_image_zoom_support', 999999);
        add_filter('pre_get_posts', 'Tigon\DmsConnect\Includes\Product_Archive_Extension::modify_sort_by_price', 999999);
        add_filter('woocommerce_catalog_orderby', 'Tigon\DmsConnect\Core::remove_popularity_sorting_option');
    }

    /**
     * Enqueue jQuery and AJAX scripts
     * @return void
     */
    public static function diagnostic_script_enqueue()
    {
        $js_url = self::asset_url();
        $ver    = defined('TIGON_DMS_VERSION') ? TIGON_DMS_VERSION : '1.0.' . filemtime(__DIR__ . '/../assets/js/tigon-dms/diagnostic.js');

        wp_register_script('tigon-dms-diagnostics', $js_url . 'diagnostic.js', array('jquery'), $ver, true);

        wp_localize_script('tigon-dms-diagnostics', 'globals', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'siteurl' => get_site_url(),
            'nonce'   => wp_create_nonce('tigon_dms_run_import_nonce'),
        ]);

        wp_enqueue_script('jquery');
        wp_enqueue_script('tigon-dms-diagnostics');
    }

    /**
     *  Remove zoom functionality from images
     */
    public static function remove_image_zoom_support() {
        remove_theme_support( 'wc-product-gallery-zoom' );
    }

    /**
     *  Remove sorting options that are not required
     */
    public static function remove_popularity_sorting_option( $orderby ) {
        unset( $orderby['popularity'] );
        unset( $orderby['rating'] );
        unset( $orderby['date'] );
        return $orderby;
    }
    

    public static function settings_script_enqueue()
    {
        $js_url = self::asset_url();
        $ver    = defined('TIGON_DMS_VERSION') ? TIGON_DMS_VERSION : '1.0.' . filemtime(__DIR__ . '/../assets/js/tigon-dms/settings.js');

        wp_register_script('tigon-dms-settings', $js_url . 'settings.js', array('jquery'), $ver, true);

        wp_localize_script('tigon-dms-settings', 'globals', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'siteurl' => get_site_url(),
            'nonce'   => wp_create_nonce('tigon_dms_run_import_nonce'),
        ]);

        wp_enqueue_script('jquery');
        wp_enqueue_script('tigon-dms-settings');
    }

    /**
     * Add taxonomy data to product during creation/update
     * @param mixed $object
     * @param mixed $request
     * @param bool $creating
     * @return void
     */
    public static function update_taxonomy($object, $request, $creating)
    {
        // Require Term ID
        $numeric_taxonomies = [
            "location",
            "manufacturers",
            "models",
            "sound-systems",
            "added-features",
            "tires",
            "vehicle-class",
            "drivetrain",
            "inventory-status"
        ];

        // Require Term Name
        $string_taxonomies = [
            "rims"
        ];

        $product_id = is_callable(array($object, 'get_id')) ? $object->get_id() : (!empty($object->ID) ? $object->ID : null);
        $params = $request->get_params();
        foreach ($numeric_taxonomies as $taxonomy) {
            $terms = isset($params[$taxonomy]) ? $params[$taxonomy] : array();

            if (!empty($terms)) {
                $terms = array_map(function ($value) {
                    return $value['id'];
                }, $terms);
                wp_set_object_terms($product_id, $terms, $taxonomy);
            }
        }
        foreach ($string_taxonomies as $taxonomy) {
            $terms = isset($params[$taxonomy]) ? $params[$taxonomy] : array();

            if (!empty($terms)) {
                $terms = array_map(function ($value) {
                    return $value['name'];
                }, $terms);
                wp_set_object_terms($product_id, $terms, $taxonomy);
            }
        }
        // ACF needs its own update
        $meta_terms = $params['meta_data']??[];
        $monroney = null;
        foreach ($meta_terms as $meta_term) {
            if ($meta_term['key'] == 'monroney_sticker')
                $monroney = $meta_term['value'];
        }

        if ($monroney)
            update_field('group_66e33328cce04', $monroney, $product_id);
    }
 
    /**
     * Applies a secondary sort by sales and model to product archives
     *
     * @param array $args
     * @return array
     */
    public static function custom_product_sort( $args ) {
        $args['orderby'] = 'meta_value';
        $args['meta_key'] = 'model';
        return $args;
    }

    /**
     * Register REST API for DMS
     * @return void
     */
    public static function register_rest_routes()
    {
        $permission_check = function () {
            return current_user_can('manage_options');
        };

        register_rest_route('tigon-dms-connect', 'used', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => 'Tigon\DmsConnect\Admin\REST_Routes::push_used_cart',
            'permission_callback' => $permission_check,
        ]);
        register_rest_route('tigon-dms-connect', 'used', [
            'methods' => \WP_REST_Server::DELETABLE,
            'callback' => 'Tigon\DmsConnect\Admin\REST_Routes::delete_used_cart',
            'permission_callback' => $permission_check,
        ]);

        register_rest_route('tigon-dms-connect', 'new/update', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => 'Tigon\DmsConnect\Admin\REST_Routes::push_new_cart',
            'permission_callback' => $permission_check,
        ]);
        register_rest_route('tigon-dms-connect', 'new/pid', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => 'Tigon\DmsConnect\Admin\REST_Routes::id_by_slug',
            'permission_callback' => $permission_check,
        ]);
        register_rest_route('tigon-dms-connect', 'showcase', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => 'Tigon\DmsConnect\Admin\REST_Routes::set_grid',
            'permission_callback' => $permission_check,
        ]);

        // Single-cart instant push — DMS sends one cart when it changes
        register_rest_route('tigon-dms-connect', 'v1/push', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => 'Tigon\DmsConnect\Admin\REST_Routes::push_single_cart',
            'permission_callback' => $permission_check,
        ]);
    }

    /**
     * Run on admin_init — if the stored DB schema version is missing
     * or older than the current plugin version, re-run install().
     * This ensures custom tables (field_mappings, cart_staging, etc.)
     * exist even after a file-only plugin update.
     */
    public static function maybe_upgrade_db(): void
    {
        $key     = 'tigon_dms_db_version';
        $current = get_option($key, '0');
        if (version_compare($current, TIGON_DMS_VERSION, '<')) {
            self::install();
            update_option($key, TIGON_DMS_VERSION);
        }
    }

    // Activation Hook
    public static function install()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        // Migrate old table names if they exist
        $old_config = $wpdb->prefix . "chimera_config";
        $new_config = $wpdb->prefix . "tigon_dms_config";
        if ($wpdb->get_var("SHOW TABLES LIKE '$old_config'") == $old_config && $wpdb->get_var("SHOW TABLES LIKE '$new_config'") != $new_config) {
            $wpdb->query("RENAME TABLE $old_config TO $new_config");
        }
        $old_lists = $wpdb->prefix . "chimera_cart_lists";
        $new_lists = $wpdb->prefix . "tigon_dms_cart_lists";
        if ($wpdb->get_var("SHOW TABLES LIKE '$old_lists'") == $old_lists && $wpdb->get_var("SHOW TABLES LIKE '$new_lists'") != $new_lists) {
            $wpdb->query("RENAME TABLE $old_lists TO $new_lists");
        }

        $table_name = $wpdb->prefix . "tigon_dms_config";

        $sql = "CREATE TABLE $table_name (
    option_id INT(6) UNSIGNED AUTO_INCREMENT,
    option_name tinytext NOT NULL,
    option_value text NOT NULL,
    PRIMARY KEY  (option_id)
    ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        ob_start();
        dbDelta($sql);

        // Create cart lists table
        $cart_list_table = $wpdb->prefix . "tigon_dms_cart_lists";
        $sql = "CREATE TABLE $cart_list_table (
    location_name varchar(32) NOT NULL,
    list_name varchar(32) NOT NULL,
    list text,
    PRIMARY KEY  (location_name, list_name)
    ) $charset_collate;";

        dbDelta($sql);
        ob_end_clean();

        // Create field mappings table
        \Tigon\DmsConnect\Admin\Field_Mapping::install();

        // Create DMS carts staging table
        \Tigon\DmsConnect\Admin\CartModel::install();

        $github_token = $wpdb->get_var("SELECT option_name FROM $table_name WHERE option_name = 'github_token'");
        if($github_token === null) $wpdb->insert(
            $table_name,
            array(
                'option_name' => 'github_token',
                'option_value' => '',
            )
        );
        $dms_url = $wpdb->get_var("SELECT option_name FROM $table_name WHERE option_name = 'dms_url'");
        if($dms_url === null) $wpdb->insert(
            $table_name,
            array(
                'option_name' => 'dms_url',
                'option_value' => '',
            )
        );
        $user_token = $wpdb->get_var("SELECT option_name FROM $table_name WHERE option_name = 'user_token'");
        if($user_token === null) $wpdb->insert(
            $table_name,
            array(
                'option_name' => 'user_token',
                'option_value' => '',
            )
        );
        $auth_token = $wpdb->get_var("SELECT option_name FROM $table_name WHERE option_name = 'auth_token'");
        if($auth_token === null) $wpdb->insert(
            $table_name,
            array(
                'option_name' => 'auth_token',
                'option_value' => '',
            )
        );
        $file_source = $wpdb->get_var("SELECT option_name FROM $table_name WHERE option_name = 'file_source'");
        if($file_source === null) $wpdb->insert(
            $table_name,
            array(
                'option_name' => 'file_source',
                'option_value' => '',
            )
        );
    }

    /**
     * AJAX handler: Sync all mapped inventory from DMS using the Database_Object engine
     *
     * Fetches all used and new carts from DMS that need to be on the website,
     * converts them via Used\Cart / New\Cart, and writes via Database_Write_Controller.
     */
    public static function ajax_sync_mapped_inventory()
    {
        check_ajax_referer('tigon_dms_sync_mapped_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);
        set_time_limit(600);

        $stats = [
            'updated' => 0,
            'created' => 0,
            'skipped' => 0,
            'errors'  => 0,
            'total'   => 0,
            'error_details' => [],
        ];

        // Fetch used carts from DMS
        try {
            $used_raw = \Tigon\DmsConnect\Includes\DMS_Connector::request(
                '{"isUsed":true, "isInStock": true, "isInBoneyard": false, "needOnWebsite": true}',
                '/chimera/lookup',
                'POST'
            );
        } catch (\Exception $e) {
            $used_raw = false;
            error_log('[DMS Sync] Failed to fetch used carts: ' . $e->getMessage());
        }
        if ($used_raw === false) {
            $stats['errors']++;
            $stats['error_details'][] = 'Failed to fetch used carts from DMS API';
        }
        $used_carts = ($used_raw !== false) ? (json_decode($used_raw, true) ?? []) : [];
        if (!is_array($used_carts)) {
            $used_carts = [];
        }

        // Fetch new carts from DMS
        try {
            $new_raw = \Tigon\DmsConnect\Includes\DMS_Connector::request(
                '{"isUsed":false, "needOnWebsite": true, "isInStock": true, "isInBoneyard": false}',
                '/chimera/lookup',
                'POST'
            );
        } catch (\Exception $e) {
            $new_raw = false;
            error_log('[DMS Sync] Failed to fetch new carts: ' . $e->getMessage());
        }
        if ($new_raw === false) {
            $stats['errors']++;
            $stats['error_details'][] = 'Failed to fetch new carts from DMS API';
        }
        $new_carts = ($new_raw !== false) ? (json_decode($new_raw, true) ?? []) : [];
        if (!is_array($new_carts)) {
            $new_carts = [];
        }

        // --- Sync used carts ---
        foreach ($used_carts as $cart) {
            $stats['total']++;

            // Stage cart in local tigon_dms_carts table
            $store_id   = $cart['cartLocation']['locationId'] ?? '';
            $store_name = \DMS_API::get_city_by_store_id($store_id);
            \Tigon\DmsConnect\Admin\CartModel::upsert_from_api($cart, $store_name, '', $store_id);

            try {
                $used = new \Tigon\DmsConnect\Admin\Used\Cart($cart);
                $converted = $used->convert();

                if (is_wp_error($converted)) {
                    $stats['errors']++;
                    $stats['error_details'][] = ($cart['_id'] ?? 'unknown') . ': ' . $converted->get_error_message();
                    continue;
                }

                $method = $converted->get_value('method');
                if ($method === 'update') {
                    $result = \Tigon\DmsConnect\Admin\Database_Write_Controller::update_from_database_object($converted);
                } else {
                    $result = \Tigon\DmsConnect\Admin\Database_Write_Controller::create_from_database_object($converted);
                }

                if (is_wp_error($result)) {
                    $stats['errors']++;
                    $stats['error_details'][] = ($cart['_id'] ?? 'unknown') . ': ' . $result->get_error_message();
                } else {
                    $stats[$method === 'update' ? 'updated' : 'created']++;

                    // Report PID back to DMS
                    if (!empty($result['pid'])) {
                        $pid_request = json_encode([[
                            '_id' => $cart['_id'] ?? '',
                            'pid' => $result['pid'],
                            'advertising' => [
                                'onWebsite'  => true,
                                'websiteUrl' => $result['websiteUrl'] ?? get_permalink($result['pid']),
                            ],
                        ]]);
                        \Tigon\DmsConnect\Includes\DMS_Connector::request($pid_request, '/chimera/carts', 'PUT');
                    }
                }
            } catch (\Exception $e) {
                $stats['errors']++;
                $stats['error_details'][] = ($cart['_id'] ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        // --- Sync new carts (deduplicated by type) ---
        $seen_new = [];
        foreach ($new_carts as $cart) {
            $stats['total']++;

            // Stage cart in local tigon_dms_carts table
            $loc_id     = $cart['cartLocation']['locationId'] ?? '';
            $loc_name   = \DMS_API::get_city_by_store_id($loc_id);
            \Tigon\DmsConnect\Admin\CartModel::upsert_from_api($cart, $loc_name, '', $loc_id);

            // Skip carts marked DELETE
            $serial = strtoupper($cart['serialNo'] ?? '');
            $vin = strtoupper($cart['vinNo'] ?? '');
            if (str_contains($serial, 'DELETE') || str_contains($vin, 'DELETE')) {
                $stats['skipped']++;
                continue;
            }

            // Skip unknown locations
            $location_id = $cart['cartLocation']['locationId'] ?? '';
            if (!isset(\Tigon\DmsConnect\Admin\Attributes::$locations[$location_id])) {
                $stats['skipped']++;
                continue;
            }

            // Deduplicate by make/model/color/seat/location
            $dedup_key = implode('|', [
                $cart['cartType']['make'] ?? '',
                $cart['cartType']['model'] ?? '',
                $cart['cartAttributes']['cartColor'] ?? '',
                $cart['cartAttributes']['seatColor'] ?? '',
                $location_id,
            ]);
            if (isset($seen_new[$dedup_key])) {
                $stats['skipped']++;
                continue;
            }
            $seen_new[$dedup_key] = true;

            try {
                \Tigon\DmsConnect\Includes\Product_Fields::define_constants();
                $result = \Tigon\DmsConnect\Abstracts\Abstract_Import_Controller::import_new($cart, 0);

                if (is_wp_error($result)) {
                    $stats['errors']++;
                    $stats['error_details'][] = ($cart['_id'] ?? 'unknown') . ': ' . $result->get_error_message();
                } else {
                    $pid = $result['pid'] ?? 0;
                    if ($pid) {
                        $stats['updated']++;
                    } else {
                        $stats['skipped']++;
                    }
                }
            } catch (\Exception $e) {
                $stats['errors']++;
                $stats['error_details'][] = ($cart['_id'] ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        // Refresh WooCommerce lookup tables
        if (function_exists('wc_update_product_lookup_tables')) {
            wc_update_product_lookup_tables();
        }

        // Clear DMS API caches so frontend serves fresh data
        if (class_exists('DMS_API')) {
            \DMS_API::clear_caches();
        }

        wp_send_json_success($stats);
    }

    /**
     * AJAX handler: Selective sync — sync New only, Used only, or All carts.
     *
     * Uses the public DMS API (/get-carts) for all carts and
     * tigon_dms_ensure_woo_product() to create/update WooCommerce products.
     */
    public static function ajax_sync_selective()
    {
        check_ajax_referer('tigon_dms_sync_selective_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);
        set_time_limit(600);

        $sync_type = sanitize_text_field($_POST['sync_type'] ?? 'all');

        $stats = [
            'updated' => 0,
            'created' => 0,
            'skipped' => 0,
            'errors'  => 0,
            'total'   => 0,
            'error_details' => [],
            'sync_type' => $sync_type,
        ];

        // Fetch all carts from public API with pagination
        $all_carts = [];
        $page = 0;
        $page_size = 50;

        while (true) {
            $batch = \DMS_API::get_carts($page, $page_size);
            if ($batch === false) {
                $stats['errors']++;
                $stats['error_details'][] = 'DMS API error on page ' . $page . ' — check API connectivity';
                break;
            }
            if (!is_array($batch) || empty($batch)) {
                break;
            }
            $all_carts = array_merge($all_carts, $batch);
            $page++;
            if (count($batch) < $page_size) {
                break;
            }
        }

        // Deduplication set for new carts (mirrors import.js + Core.php sync)
        $seen_new = [];

        foreach ($all_carts as $cart) {
            $cart_id = $cart['_id'] ?? '';
            if (empty($cart_id)) {
                continue;
            }

            $is_used = !empty($cart['isUsed']);

            // Filter by sync type
            if ($sync_type === 'new' && $is_used) {
                continue;
            }
            if ($sync_type === 'used' && !$is_used) {
                continue;
            }

            // ---------------------------------------------------------
            // Cart eligibility — delete product if cart is ineligible
            // ---------------------------------------------------------
            if (!\Tigon\DmsConnect\Includes\Product_Manager::should_be_on_website($cart)) {
                $existing_pid = function_exists('tigon_dms_get_product_by_cart_id')
                    ? tigon_dms_get_product_by_cart_id($cart_id)
                    : false;
                if ($existing_pid) {
                    \Tigon\DmsConnect\Includes\Product_Media::delete_product((int) $existing_pid);
                }
                continue;
            }
            $serial = strtoupper($cart['serialNo'] ?? '');
            $vin    = strtoupper($cart['vinNo'] ?? '');
            if (str_contains($serial, 'DELETE') || str_contains($vin, 'DELETE')) {
                continue;
            }

            // Deduplicate new carts by make/model/color/seat/location
            if (!$is_used) {
                $loc_id = $cart['cartLocation']['locationId'] ?? '';
                $dedup_key = implode('|', [
                    $cart['cartType']['make'] ?? '',
                    $cart['cartType']['model'] ?? '',
                    $cart['cartAttributes']['cartColor'] ?? '',
                    $cart['cartAttributes']['seatColor'] ?? '',
                    $loc_id,
                ]);
                if (isset($seen_new[$dedup_key])) {
                    continue;
                }
                $seen_new[$dedup_key] = true;
            }

            $stats['total']++;

            // Stage in local table
            $store_id   = $cart['cartLocation']['locationId'] ?? '';
            $store_name = \DMS_API::get_city_by_store_id($store_id);
            try {
                \Tigon\DmsConnect\Admin\CartModel::upsert_from_api($cart, $store_name, '', $store_id);
            } catch (\Throwable $e) {
                // Non-fatal: cart staging failed but product sync can continue
                error_log('[DMS Selective Sync] CartModel upsert failed for ' . $cart_id . ': ' . $e->getMessage());
            }

            // Check if product already exists
            $existing = function_exists('tigon_dms_get_product_by_cart_id')
                ? tigon_dms_get_product_by_cart_id($cart_id)
                : false;
            $was_existing = (bool) $existing;

            try {
                if (function_exists('tigon_dms_ensure_woo_product')) {
                    $product_id = tigon_dms_ensure_woo_product($cart, $cart_id);
                } else {
                    $stats['errors']++;
                    $stats['error_details'][] = $cart_id . ': tigon_dms_ensure_woo_product not found';
                    continue;
                }

                if ($product_id) {
                    // Sync images
                    if (class_exists('\DMS_Sync')) {
                        \DMS_Sync::sync_product_images_public($product_id, $cart);
                    }
                    $stats[$was_existing ? 'updated' : 'created']++;
                } else {
                    $stats['errors']++;
                    $stats['error_details'][] = $cart_id . ': product creation returned false';
                }
            } catch (\Exception $e) {
                $stats['errors']++;
                $stats['error_details'][] = $cart_id . ': ' . $e->getMessage();
            } catch (\Error $e) {
                $stats['errors']++;
                $stats['error_details'][] = $cart_id . ': [Fatal] ' . $e->getMessage();
            }
        }

        // Refresh WooCommerce lookup tables
        if (function_exists('wc_update_product_lookup_tables')) {
            wc_update_product_lookup_tables();
        }

        // Clear DMS API caches so frontend serves fresh data
        if (class_exists('DMS_API')) {
            \DMS_API::clear_caches();
        }

        wp_send_json_success($stats);
    }

    // ─────────────────────────────────────────────────────────────────
    //  Batched Selective Sync (timeout-safe, page-by-page)
    // ─────────────────────────────────────────────────────────────────

    /**
     * AJAX: Initialize a batched selective sync.
     *
     * ONE lightweight API call to get the total cart count. Stores sync
     * metadata in a small transient. Each batch then fetches and processes
     * only 5 carts at a time (page-cache approach) so every request
     * finishes well within Cloudflare's 100-second proxy timeout.
     */
    public static function ajax_sync_selective_init()
    {
        check_ajax_referer('tigon_dms_sync_selective_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        try {
            $sync_type  = sanitize_text_field($_POST['sync_type'] ?? 'all');
            $page_size  = 50;  // API page size (fetched once, cached)
            $batch_size = 5;   // carts processed per AJAX request

            // One API call to discover total carts
            $page_data = \DMS_API::get_carts_page(0, $page_size);

            if ($page_data === false) {
                wp_send_json_error('DMS API unreachable — check API connectivity and try again.');
                return;
            }

            $total_carts = $page_data['total'] ?? 0;

            // Store sync session metadata
            $sync_id = 'dms_sync_' . wp_generate_password(16, false);
            set_transient($sync_id, [
                'sync_type'      => $sync_type,
                'page_size'      => $page_size,
                'batch_size'     => $batch_size,
                'seen_new'       => [],
                'total_carts'    => $total_carts,
                'current_page'   => 0,
                'page_cursor'    => 0,
                'page_cache'     => $page_data['carts'] ?? [],
                'pages_done'     => false,
                'processed'      => 0,
            ], HOUR_IN_SECONDS);

            wp_send_json_success([
                'sync_id'    => $sync_id,
                'total'      => $total_carts,
                'batch_size' => $batch_size,
                'sync_type'  => $sync_type,
            ]);
        } catch (\Throwable $e) {
            wp_send_json_error('Init error: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Process a micro-batch of carts (5 at a time).
     *
     * Uses a page-cache approach: fetches 50 carts from the API once,
     * caches them in the transient, then processes 5 per request.
     * When the cached page is exhausted, fetches the next API page.
     * This keeps every request under 60 seconds (well within Cloudflare's
     * 100-second limit).
     */
    public static function ajax_sync_selective_batch()
    {
        check_ajax_referer('tigon_dms_sync_selective_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);
        set_time_limit(90);

        try {
            $sync_id = sanitize_text_field($_POST['sync_id'] ?? '');

            $meta = get_transient($sync_id);
            if (!is_array($meta)) {
                wp_send_json_error('Sync session expired or invalid. Please start a new sync.');
                return;
            }

            $sync_type    = $meta['sync_type'] ?? 'all';
            $page_size    = $meta['page_size'] ?? 50;
            $batch_size   = $meta['batch_size'] ?? 5;
            $seen_new     = $meta['seen_new'] ?? [];
            $current_page = $meta['current_page'] ?? 0;
            $page_cursor  = $meta['page_cursor'] ?? 0;
            $page_cache   = $meta['page_cache'] ?? [];
            $pages_done   = $meta['pages_done'] ?? false;
            $total_processed = $meta['processed'] ?? 0;

            // If page cache is exhausted, fetch the next API page
            if ($page_cursor >= count($page_cache) && !$pages_done) {
                $current_page++;
                $page_cache = \DMS_API::get_carts($current_page, $page_size);
                if ($page_cache === false) {
                    $page_cache = [];
                }
                if (!is_array($page_cache)) {
                    $page_cache = [];
                }
                $page_cursor = 0;

                // If this page is short or empty, no more pages
                if (count($page_cache) < $page_size) {
                    $pages_done = true;
                }
            }

            // Slice the micro-batch from the cached page
            $batch = array_slice($page_cache, $page_cursor, $batch_size);
            $page_cursor += count($batch);

            $stats = [
                'created'       => 0,
                'updated'       => 0,
                'skipped'       => 0,
                'errors'        => 0,
                'error_details' => [],
                'skip_details'  => [],
            ];

            foreach ($batch as $cart) {
                $cart_id = $cart['_id'] ?? '';
                if (empty($cart_id)) {
                    $stats['skipped']++;
                    $stats['skip_details'][] = '(unknown cart): Missing _id field';
                    continue;
                }

                // Build a human-readable label for skip messages
                $make  = $cart['cartType']['make'] ?? '';
                $model = $cart['cartType']['model'] ?? '';
                $color = $cart['cartAttributes']['cartColor'] ?? '';
                $serial = $cart['serialNo'] ?? '';
                $cart_label = trim("{$make} {$model}") ?: $cart_id;
                if ($color) $cart_label .= " ({$color})";
                if ($serial) $cart_label .= " SN:{$serial}";

                $is_used = !empty($cart['isUsed']);

                // Filter by sync type
                if ($sync_type === 'new' && $is_used) {
                    $stats['skipped']++;
                    $stats['skip_details'][] = "{$cart_label}: Skipped — used cart (syncing new only)";
                    continue;
                }
                if ($sync_type === 'used' && !$is_used) {
                    $stats['skipped']++;
                    $stats['skip_details'][] = "{$cart_label}: Skipped — new cart (syncing used only)";
                    continue;
                }

                // Eligibility check — delete product if ineligible
                if (!\Tigon\DmsConnect\Includes\Product_Manager::should_be_on_website($cart)) {
                    $existing_pid = function_exists('tigon_dms_get_product_by_cart_id')
                        ? tigon_dms_get_product_by_cart_id($cart_id)
                        : false;
                    if ($existing_pid) {
                        \Tigon\DmsConnect\Includes\Product_Media::delete_product((int) $existing_pid);
                        $stats['skip_details'][] = "{$cart_label}: Deleted — not eligible for website";
                    } else {
                        $stats['skipped']++;
                        $stats['skip_details'][] = "{$cart_label}: Skipped — not eligible for website";
                    }
                    continue;
                }
                $serial_upper = strtoupper($cart['serialNo'] ?? '');
                $vin    = strtoupper($cart['vinNo'] ?? '');
                if (str_contains($serial_upper, 'DELETE') || str_contains($vin, 'DELETE')) {
                    $stats['skipped']++;
                    $stats['skip_details'][] = "{$cart_label}: Skipped — serial/VIN contains DELETE";
                    continue;
                }

                // Deduplicate new carts (persisted across pages via meta)
                if (!$is_used) {
                    $loc_id = $cart['cartLocation']['locationId'] ?? '';
                    $dedup_key = implode('|', [
                        $cart['cartType']['make'] ?? '',
                        $cart['cartType']['model'] ?? '',
                        $cart['cartAttributes']['cartColor'] ?? '',
                        $cart['cartAttributes']['seatColor'] ?? '',
                        $loc_id,
                    ]);
                    if (isset($seen_new[$dedup_key])) {
                        $stats['skipped']++;
                        $stats['skip_details'][] = "{$cart_label}: Skipped — duplicate new cart (same make/model/color/seat/location)";
                        continue;
                    }
                    $seen_new[$dedup_key] = true;
                }

                // Stage in local table
                $store_id   = $cart['cartLocation']['locationId'] ?? '';
                $store_name = \DMS_API::get_city_by_store_id($store_id);
                try {
                    \Tigon\DmsConnect\Admin\CartModel::upsert_from_api($cart, $store_name, '', $store_id);
                } catch (\Throwable $e) {
                    error_log('[DMS Batched Sync] CartModel upsert failed for ' . $cart_id . ': ' . $e->getMessage());
                }

                // Check if product already exists
                $existing = function_exists('tigon_dms_get_product_by_cart_id')
                    ? tigon_dms_get_product_by_cart_id($cart_id)
                    : false;
                $was_existing = (bool) $existing;

                try {
                    if (function_exists('tigon_dms_ensure_woo_product')) {
                        $product_id = tigon_dms_ensure_woo_product($cart, $cart_id);
                    } else {
                        $stats['errors']++;
                        $stats['error_details'][] = $cart_id . ': tigon_dms_ensure_woo_product not found';
                        continue;
                    }

                    if ($product_id) {
                        if (class_exists('\DMS_Sync')) {
                            \DMS_Sync::sync_product_images_public($product_id, $cart);
                        }
                        $stats[$was_existing ? 'updated' : 'created']++;
                    } else {
                        $stats['errors']++;
                        $stats['error_details'][] = $cart_id . ': product creation returned false';
                    }
                } catch (\Exception $e) {
                    $stats['errors']++;
                    $stats['error_details'][] = $cart_id . ': ' . $e->getMessage();
                } catch (\Error $e) {
                    $stats['errors']++;
                    $stats['error_details'][] = $cart_id . ': [Fatal] ' . $e->getMessage();
                }
            }

            $total_processed += count($batch);

            // Done when: pages exhausted AND page cache cursor has reached the end
            $done = $pages_done && ($page_cursor >= count($page_cache));

            // Persist updated state
            $meta['seen_new']     = $seen_new;
            $meta['current_page'] = $current_page;
            $meta['page_cursor']  = $page_cursor;
            $meta['page_cache']   = $page_cache;
            $meta['pages_done']   = $pages_done;
            $meta['processed']    = $total_processed;
            set_transient($sync_id, $meta, HOUR_IN_SECONDS);

            // Clean up and finalize on last batch
            if ($done) {
                delete_transient($sync_id);
                if (function_exists('wc_update_product_lookup_tables')) {
                    wc_update_product_lookup_tables();
                }
                if (class_exists('DMS_API')) {
                    \DMS_API::clear_caches();
                }
            }

            wp_send_json_success(array_merge($stats, [
                'processed' => $total_processed,
                'done'      => $done,
            ]));
        } catch (\Throwable $e) {
            wp_send_json_error('Batch error: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────
    //  Batched Mapped Sync (timeout-safe)
    // ─────────────────────────────────────────────────────────────────

    /**
     * AJAX: Initialize a batched mapped sync.
     *
     * Fetches all used + new carts via the DMS Connector (authenticated API),
     * stores them in a transient, and returns the total count.
     */
    public static function ajax_sync_mapped_init()
    {
        check_ajax_referer('tigon_dms_sync_mapped_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        try {
        ignore_user_abort(true);
        set_time_limit(300);

        $errors = [];

        // Fetch used carts
        $used_carts = [];
        try {
            $used_raw = \Tigon\DmsConnect\Includes\DMS_Connector::request(
                '{"isUsed":true, "isInStock": true, "isInBoneyard": false, "needOnWebsite": true}',
                '/chimera/lookup',
                'POST'
            );
            if ($used_raw !== false) {
                $used_carts = json_decode($used_raw, true) ?? [];
                if (!is_array($used_carts)) {
                    $used_carts = [];
                }
            } else {
                $errors[] = 'Failed to fetch used carts from DMS API';
            }
        } catch (\Exception $e) {
            $errors[] = 'Used cart fetch error: ' . $e->getMessage();
        }

        // Fetch new carts
        $new_carts = [];
        try {
            $new_raw = \Tigon\DmsConnect\Includes\DMS_Connector::request(
                '{"isUsed":false, "needOnWebsite": true, "isInStock": true, "isInBoneyard": false}',
                '/chimera/lookup',
                'POST'
            );
            if ($new_raw !== false) {
                $new_carts = json_decode($new_raw, true) ?? [];
                if (!is_array($new_carts)) {
                    $new_carts = [];
                }
            } else {
                $errors[] = 'Failed to fetch new carts from DMS API';
            }
        } catch (\Exception $e) {
            $errors[] = 'New cart fetch error: ' . $e->getMessage();
        }

        // Tag each cart with its type for the batch processor
        foreach ($used_carts as &$c) {
            $c['_sync_type'] = 'used';
        }
        unset($c);

        // Deduplicate new carts
        $seen_new = [];
        $filtered_new = [];
        foreach ($new_carts as $cart) {
            $serial = strtoupper($cart['serialNo'] ?? '');
            $vin = strtoupper($cart['vinNo'] ?? '');
            if (str_contains($serial, 'DELETE') || str_contains($vin, 'DELETE')) {
                continue;
            }
            $location_id = $cart['cartLocation']['locationId'] ?? '';
            if (!isset(\Tigon\DmsConnect\Admin\Attributes::$locations[$location_id])) {
                continue;
            }
            $dedup_key = implode('|', [
                $cart['cartType']['make'] ?? '',
                $cart['cartType']['model'] ?? '',
                $cart['cartAttributes']['cartColor'] ?? '',
                $cart['cartAttributes']['seatColor'] ?? '',
                $location_id,
            ]);
            if (isset($seen_new[$dedup_key])) {
                continue;
            }
            $seen_new[$dedup_key] = true;
            $cart['_sync_type'] = 'new';
            $filtered_new[] = $cart;
        }

        // Combine used + filtered new
        $all_carts = array_merge($used_carts, $filtered_new);

        // Store in transient (structured: carts + offset for server-managed batching)
        $sync_id = 'dms_msync_' . wp_generate_password(16, false);
        set_transient($sync_id, [
            'carts'      => $all_carts,
            'offset'     => 0,
            'batch_size' => 3,
        ], HOUR_IN_SECONDS);

        wp_send_json_success([
            'sync_id'    => $sync_id,
            'total'      => count($all_carts),
            'batch_size' => 3,
            'used_count' => count($used_carts),
            'new_count'  => count($filtered_new),
            'errors'     => $errors,
        ]);
        } catch (\Throwable $e) {
            wp_send_json_error('Mapped sync init error: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Process one batch in a batched mapped sync.
     */
    public static function ajax_sync_mapped_batch()
    {
        check_ajax_referer('tigon_dms_sync_mapped_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        try {
        ignore_user_abort(true);
        set_time_limit(90);

        $sync_id = sanitize_text_field($_POST['sync_id'] ?? '');

        $meta = get_transient($sync_id);
        if (!is_array($meta) || !isset($meta['carts'])) {
            wp_send_json_error('Sync session expired or invalid. Please start a new sync.');
            return;
        }

        $carts      = $meta['carts'];
        $offset     = $meta['offset'] ?? 0;
        $batch_size = $meta['batch_size'] ?? 3;

        $batch = array_slice($carts, $offset, $batch_size);
        $stats = [
            'created'       => 0,
            'updated'       => 0,
            'skipped'       => 0,
            'errors'        => 0,
            'error_details' => [],
        ];

        foreach ($batch as $cart) {
            $sync_type = $cart['_sync_type'] ?? 'used';
            unset($cart['_sync_type']);

            // Stage in local table
            $store_id   = $cart['cartLocation']['locationId'] ?? '';
            $store_name = \DMS_API::get_city_by_store_id($store_id);
            \Tigon\DmsConnect\Admin\CartModel::upsert_from_api($cart, $store_name, '', $store_id);

            try {
                if ($sync_type === 'used') {
                    $used = new \Tigon\DmsConnect\Admin\Used\Cart($cart);
                    $converted = $used->convert();

                    if (is_wp_error($converted)) {
                        $stats['errors']++;
                        $stats['error_details'][] = ($cart['_id'] ?? 'unknown') . ': ' . $converted->get_error_message();
                        continue;
                    }

                    $method = $converted->get_value('method');
                    if ($method === 'update') {
                        $result = \Tigon\DmsConnect\Admin\Database_Write_Controller::update_from_database_object($converted);
                    } else {
                        $result = \Tigon\DmsConnect\Admin\Database_Write_Controller::create_from_database_object($converted);
                    }

                    if (is_wp_error($result)) {
                        $stats['errors']++;
                        $stats['error_details'][] = ($cart['_id'] ?? 'unknown') . ': ' . $result->get_error_message();
                    } else {
                        $stats[$method === 'update' ? 'updated' : 'created']++;

                        // Report PID back to DMS
                        if (!empty($result['pid'])) {
                            $pid_request = json_encode([[
                                '_id' => $cart['_id'] ?? '',
                                'pid' => $result['pid'],
                                'advertising' => [
                                    'onWebsite'  => true,
                                    'websiteUrl' => $result['websiteUrl'] ?? get_permalink($result['pid']),
                                ],
                            ]]);
                            try {
                                \Tigon\DmsConnect\Includes\DMS_Connector::request($pid_request, '/chimera/carts', 'PUT');
                            } catch (\Exception $e) {
                                // Non-fatal
                            }
                        }
                    }
                } else {
                    // New cart
                    \Tigon\DmsConnect\Includes\Product_Fields::define_constants();
                    $result = \Tigon\DmsConnect\Abstracts\Abstract_Import_Controller::import_new($cart, 0);

                    if (is_wp_error($result)) {
                        $stats['errors']++;
                        $stats['error_details'][] = ($cart['_id'] ?? 'unknown') . ': ' . $result->get_error_message();
                    } else {
                        $pid = $result['pid'] ?? 0;
                        $stats[$pid ? 'updated' : 'skipped']++;
                    }
                }
            } catch (\Exception $e) {
                $stats['errors']++;
                $stats['error_details'][] = ($cart['_id'] ?? 'unknown') . ': ' . $e->getMessage();
            }
        }

        $new_offset = $offset + count($batch);
        $done = $new_offset >= count($carts);

        if ($done) {
            delete_transient($sync_id);
            if (function_exists('wc_update_product_lookup_tables')) {
                wc_update_product_lookup_tables();
            }
            if (class_exists('DMS_API')) {
                \DMS_API::clear_caches();
            }
        } else {
            // Persist updated offset
            $meta['offset'] = $new_offset;
            set_transient($sync_id, $meta, HOUR_IN_SECONDS);
        }

        wp_send_json_success(array_merge($stats, [
            'processed' => $new_offset,
            'total'     => count($carts),
            'done'      => $done,
        ]));
        } catch (\Throwable $e) {
            wp_send_json_error('Mapped batch error: ' . $e->getMessage());
        }
    }

    // ─────────────────────────────────────────────────────────────────
    //  Publish Synced Inventory — micro-batched (Cloudflare-safe)
    //
    //  Cloudflare CDN enforces a hard ~100 s gateway timeout (free/pro
    //  plans) and many managed-WP hosts cap PHP at 60 s.  Each
    //  wp_update_post() fires WooCommerce save hooks, cache purges,
    //  and term re-counts that can take 5-15 s per product, so even
    //  10 products per batch blows through the window.
    //
    //  Strategy:
    //    • Batch size = 3  (safe at ≤15 s/product → ≤45 s total)
    //    • Direct $wpdb->update for the status flip (avoids the full
    //      wp_update_post hook chain); we still call clean_post_cache
    //      so WP/WC sees the change.
    //    • wp_defer_term_counting while the batch runs.
    //    • Lookup-table rebuild only on the final batch.
    // ─────────────────────────────────────────────────────────────────

    /** Batch size for publish — intentionally tiny for CDN safety. */
    const PUBLISH_BATCH_SIZE = 3;

    /**
     * AJAX: Init — gather DMS-synced product IDs, store in transient.
     *
     * Only products that have a `_dms_cart_id` meta key are included —
     * i.e. only inventory that actually came from the DMS API.
     */
    public static function ajax_publish_synced_init()
    {
        check_ajax_referer('tigon_dms_publish_synced_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        global $wpdb;

        try {
            // Only products with _dms_cart_id (came from DMS API)
            $all_ids = $wpdb->get_col(
                "SELECT DISTINCT p.ID
                 FROM {$wpdb->posts} p
                 INNER JOIN {$wpdb->postmeta} pm
                    ON p.ID = pm.post_id AND pm.meta_key = '_dms_cart_id'
                 WHERE p.post_type = 'product'"
            );

            if (empty($all_ids)) {
                wp_send_json_success([
                    'total'      => 0,
                    'to_publish' => 0,
                    'done'       => true,
                    'message'    => 'No DMS-synced products found.',
                ]);
                return;
            }

            $to_publish = $wpdb->get_var(
                "SELECT COUNT(DISTINCT p.ID)
                 FROM {$wpdb->posts} p
                 INNER JOIN {$wpdb->postmeta} pm
                    ON p.ID = pm.post_id AND pm.meta_key = '_dms_cart_id'
                 WHERE p.post_type = 'product'
                   AND p.post_status != 'publish'"
            );

            $sync_id = 'dms_pub_' . wp_generate_password(16, false);
            set_transient($sync_id, [
                'ids'    => array_map('intval', $all_ids),
                'offset' => 0,
            ], HOUR_IN_SECONDS);

            wp_send_json_success([
                'sync_id'    => $sync_id,
                'total'      => count($all_ids),
                'to_publish' => (int) $to_publish,
                'batch_size' => self::PUBLISH_BATCH_SIZE,
            ]);
        } catch (\Throwable $e) {
            wp_send_json_error('Publish init error: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Batch — publish + feature products, 3 at a time.
     *
     * Every DB operation is direct SQL — no wp_update_post, no
     * wp_set_object_terms — so the only cost per product is a
     * handful of cheap queries and one cache flush.
     *
     * An elapsed-time guard bails out early if we approach the
     * 45 s safety ceiling so the response always gets back to
     * the browser before Cloudflare kills the connection.
     */
    public static function ajax_publish_synced_batch()
    {
        check_ajax_referer('tigon_dms_publish_synced_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);
        @set_time_limit(55);

        global $wpdb;

        // ── Time guard: bail before Cloudflare / PHP kills us ──
        $start    = microtime(true);
        $max_secs = 40; // leave ~15 s headroom for CF + PHP overhead

        try {
            $sync_id = sanitize_text_field($_POST['sync_id'] ?? '');
            $meta = get_transient($sync_id);
            if (!is_array($meta) || !isset($meta['ids'])) {
                wp_send_json_error('Session expired or invalid. Please start again.');
                return;
            }

            $ids    = $meta['ids'];
            $offset = $meta['offset'] ?? 0;
            $batch  = array_slice($ids, $offset, self::PUBLISH_BATCH_SIZE);

            // Look up the featured term_taxonomy_id once for all products
            $featured_tt_id = (int) $wpdb->get_var(
                "SELECT tt.term_taxonomy_id
                 FROM {$wpdb->term_taxonomy} tt
                 INNER JOIN {$wpdb->terms} t ON tt.term_id = t.term_id
                 WHERE tt.taxonomy = 'product_visibility'
                   AND t.slug = 'featured'
                 LIMIT 1"
            );

            $published = 0;
            $featured  = 0;
            $already   = 0;
            $errors    = [];
            $processed_count = 0;

            foreach ($batch as $pid) {
                // ── Time guard: stop before we hit the ceiling ──
                if ((microtime(true) - $start) >= $max_secs) {
                    break;
                }

                $processed_count++;

                $status = get_post_status($pid);
                if ($status === false) {
                    $errors[] = "Product #{$pid}: not found";
                    continue;
                }

                try {
                    // ── Publish via direct DB update (skip heavy hooks) ──
                    if ($status !== 'publish') {
                        // Check if the product has missing required mappings
                        $readiness_meta = get_post_meta($pid, '_dms_readiness_missing', true);
                        if (!empty($readiness_meta)) {
                            $errors[] = "#{$pid}: skipped (missing mappings)";
                            continue;
                        }

                        $updated = $wpdb->update(
                            $wpdb->posts,
                            ['post_status' => 'publish'],
                            ['ID' => $pid],
                            ['%s'],
                            ['%d']
                        );
                        if ($updated === false) {
                            $errors[] = "#{$pid}: DB update failed";
                            continue;
                        }
                        clean_post_cache($pid);
                        $published++;
                    } else {
                        $already++;
                    }

                    // ── Ensure "featured" visibility term (pure SQL) ──
                    if ($featured_tt_id > 0) {
                        $has_featured = (bool) $wpdb->get_var($wpdb->prepare(
                            "SELECT 1
                             FROM {$wpdb->term_relationships}
                             WHERE object_id = %d
                               AND term_taxonomy_id = %d
                             LIMIT 1",
                            $pid,
                            $featured_tt_id
                        ));
                        if (!$has_featured) {
                            // Direct insert into term_relationships
                            $wpdb->replace(
                                $wpdb->term_relationships,
                                [
                                    'object_id'        => $pid,
                                    'term_taxonomy_id' => $featured_tt_id,
                                    'term_order'       => 0,
                                ],
                                ['%d', '%d', '%d']
                            );
                            // Bump the count on the taxonomy row
                            $wpdb->query($wpdb->prepare(
                                "UPDATE {$wpdb->term_taxonomy}
                                 SET count = count + 1
                                 WHERE term_taxonomy_id = %d",
                                $featured_tt_id
                            ));
                            update_post_meta($pid, '_visibility', 'visible');
                            $featured++;
                        }
                    }
                } catch (\Throwable $e) {
                    $errors[] = "#{$pid}: " . $e->getMessage();
                }
            }

            // Advance offset by however many we actually touched
            $new_offset = $offset + $processed_count;
            $done = $new_offset >= count($ids);

            if ($done) {
                delete_transient($sync_id);
                // Rebuild WC lookup tables once at the very end
                if (function_exists('wc_update_product_lookup_tables')) {
                    wc_update_product_lookup_tables();
                }
                // Clean term taxonomy counts in one shot
                if ($featured_tt_id > 0) {
                    wp_update_term_count_now([$featured_tt_id], 'product_visibility');
                }
            } else {
                $meta['offset'] = $new_offset;
                set_transient($sync_id, $meta, HOUR_IN_SECONDS);
            }

            wp_send_json_success([
                'published' => $published,
                'featured'  => $featured,
                'already'   => $already,
                'errors'    => $errors,
                'processed' => $new_offset,
                'total'     => count($ids),
                'done'      => $done,
            ]);
        } catch (\Throwable $e) {
            wp_send_json_error('Publish batch error: ' . $e->getMessage());
        }
    }

    /**
     * Enqueue scripts for the Field Mapping admin page.
     */
    public static function field_mapping_script_enqueue()
    {
        // Field mapping page only needs jQuery + the globals localized data.
        // Register a tiny stub so wp_localize_script has a handle to attach to.
        $js_url = self::asset_url();
        wp_register_script('tigon-dms-globals', $js_url . 'globals.js', array(), '1.0', false);

        wp_localize_script('tigon-dms-globals', 'globals', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'siteurl' => get_site_url(),
            'nonce'   => wp_create_nonce('tigon_dms_run_import_nonce'),
        ]);

        wp_enqueue_script('tigon-dms-globals');
        wp_enqueue_script('jquery');
    }

    /**
     * Enqueue scripts for the Sync admin page.
     */
    public static function sync_script_enqueue()
    {
        wp_enqueue_script('jquery');
    }

    // ------------------------------------------------------------------
    //  Field Mapping AJAX handlers
    // ------------------------------------------------------------------

    /**
     * AJAX: Return all field mappings + known field lists for the UI.
     */
    public static function ajax_get_field_mappings()
    {
        check_ajax_referer('tigon_dms_field_mapping_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        wp_send_json_success([
            'mappings'    => \Tigon\DmsConnect\Admin\Field_Mapping::get_all(),
            'dms_fields'  => \Tigon\DmsConnect\Admin\Field_Mapping::get_known_dms_fields(),
            'woo_targets' => \Tigon\DmsConnect\Admin\Field_Mapping::get_known_woo_targets(),
        ]);
    }

    /**
     * AJAX: Insert or update a single field mapping.
     */
    public static function ajax_save_field_mapping()
    {
        check_ajax_referer('tigon_dms_field_mapping_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        $mapping_id = intval($_POST['mapping_id'] ?? 0);
        $row = [
            'dms_path'      => sanitize_text_field($_POST['dms_path'] ?? ''),
            'woo_target'    => sanitize_text_field($_POST['woo_target'] ?? ''),
            'target_type'   => sanitize_text_field($_POST['target_type'] ?? 'postmeta'),
            'transform'     => sanitize_text_field($_POST['transform'] ?? 'direct'),
            'transform_cfg' => wp_kses_post($_POST['transform_cfg'] ?? ''),
            'is_enabled'    => intval($_POST['is_enabled'] ?? 1),
            'sort_order'    => intval($_POST['sort_order'] ?? 0),
        ];

        if ($mapping_id > 0) {
            $ok = \Tigon\DmsConnect\Admin\Field_Mapping::update($mapping_id, $row);
            wp_send_json_success(['updated' => $ok, 'mapping_id' => $mapping_id]);
        } else {
            $new_id = \Tigon\DmsConnect\Admin\Field_Mapping::insert($row);
            if ($new_id) {
                wp_send_json_success(['mapping_id' => $new_id]);
            } else {
                wp_send_json_error('Failed to insert mapping');
            }
        }
    }

    /**
     * AJAX: Delete a single field mapping.
     */
    public static function ajax_delete_field_mapping()
    {
        check_ajax_referer('tigon_dms_field_mapping_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        $mapping_id = intval($_POST['mapping_id'] ?? 0);
        if ($mapping_id <= 0) {
            wp_send_json_error('Invalid mapping ID');
        }

        $ok = \Tigon\DmsConnect\Admin\Field_Mapping::delete($mapping_id);
        wp_send_json_success(['deleted' => $ok]);
    }

    /**
     * AJAX: Return the known DMS fields and WooCommerce targets for dropdowns.
     */
    public static function ajax_get_mapping_meta()
    {
        check_ajax_referer('tigon_dms_field_mapping_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        wp_send_json_success([
            'dms_fields'  => \Tigon\DmsConnect\Admin\Field_Mapping::get_known_dms_fields(),
            'woo_targets' => \Tigon\DmsConnect\Admin\Field_Mapping::get_known_woo_targets(),
            'transforms'  => [
                'direct'        => 'Direct (pass-through)',
                'uppercase'     => 'UPPERCASE',
                'lowercase'     => 'lowercase',
                'ucwords'       => 'Ucwords (Title Case)',
                'boolean_yesno' => 'Boolean → Yes/No',
                'boolean_label' => 'Boolean → Custom Labels',
                'prefix'        => 'Prefix (prepend text)',
                'suffix'        => 'Suffix (append text)',
                'template'      => 'Template ({value} placeholder)',
                'static'        => 'Static Value (ignore DMS field)',
            ],
        ]);
    }

    // Deactivation Hook
    public static function deactivate()
    {
        // Clear scheduled sync events
        wp_clear_scheduled_hook('tigon_dms_sync_inventory');
    }

    /**
     * Uninstall Hook — clean up all plugin data from the database.
     *
     * Drops custom tables and removes all DMS-related transients.
     * Product data (posts, postmeta, attachments) is left in place
     * to avoid accidental data loss. Use the admin UI to bulk-delete
     * products before uninstalling if you want a clean removal.
     */
    public static function uninstall()
    {
        global $wpdb;

        // Drop custom database tables
        $tables = [
            $wpdb->prefix . 'tigon_dms_config',
            $wpdb->prefix . 'tigon_dms_cart_lists',
            $wpdb->prefix . 'tigon_dms_field_mappings',
            $wpdb->prefix . 'tigon_dms_carts',
        ];

        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$table}");
        }

        // Clear all DMS-related transients
        if (class_exists('DMS_API')) {
            \DMS_API::clear_caches();
        }

        // Clear scheduled events
        wp_clear_scheduled_hook('tigon_dms_sync_inventory');
    }
}
