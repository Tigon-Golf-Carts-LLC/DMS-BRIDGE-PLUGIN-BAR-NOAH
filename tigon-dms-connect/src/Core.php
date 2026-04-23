<?php

namespace Tigon\DmsConnect;

class Core
{

    private function __contruct()
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
        add_action('load-tigon-dms-connect_page_import', 'Tigon\DmsConnect\Core::import_script_enqueue');
        add_action('load-tigon-dms-connect_page_settings', 'Tigon\DmsConnect\Core::settings_script_enqueue');

        // Register Ajax functions
        add_action('wp_ajax_tigon_dms_query', 'Tigon\DmsConnect\Admin\Ajax_Import_Controller::query_dms');
        add_action('wp_ajax_tigon_dms_get_new_carts', 'Tigon\DmsConnect\Admin\New\New_Cart_Converter::generate_new_carts');
        add_action('wp_ajax_tigon_dms_ajax_import_convert', 'Tigon\DmsConnect\Admin\Ajax_Import_Controller::ajax_import_convert');
        add_action('wp_ajax_tigon_dms_ajax_new_import_convert', 'Tigon\DmsConnect\Admin\Ajax_Import_Controller::ajax_new_import_convert');
        add_action('wp_ajax_tigon_dms_ajax_import_create', 'Tigon\DmsConnect\Admin\Ajax_Import_Controller::ajax_import_create');
        add_action('wp_ajax_tigon_dms_ajax_import_update', 'Tigon\DmsConnect\Admin\Ajax_Import_Controller::ajax_import_update');
        add_action('wp_ajax_tigon_dms_ajax_import_delete', 'Tigon\DmsConnect\Admin\Ajax_Import_Controller::ajax_import_delete');
        add_action('wp_ajax_tigon_dms_ajax_import_new', 'Tigon\DmsConnect\Admin\Ajax_Import_Controller::ajax_import_new');
        add_action('wp_ajax_tigon_dms_save_settings', 'Tigon\DmsConnect\Admin\Ajax_Settings_Controller::save_settings');
        add_action('wp_ajax_tigon_dms_get_dms_props', 'Tigon\DmsConnect\Admin\Ajax_Settings_Controller::get_dms_props');
        add_action('wp_ajax_tigon_dms_post_import', 'Tigon\DmsConnect\Admin\Ajax_Import_Controller::process_post_import');
        add_action('wp_ajax_tigon_dms_sync_mapped', 'Tigon\DmsConnect\Core::ajax_sync_mapped_inventory');

        // Add admin page
        add_action('admin_menu', 'Tigon\DmsConnect\Admin\Admin_Page::add_menu_page');

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
                    'slug' => 'dms-bridge-plugin/dms-bridge-plugin.php',
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
        wp_register_script('@tigon-dms/globals', $js_url . 'globals.js');
        wp_register_script_module('@tigon-dms/diagnostics', $js_url . 'diagnostic.js', array('jquery'));

        wp_localize_script('@tigon-dms/globals', 'globals', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'siteurl' => get_site_url()
        ]);

        wp_enqueue_script('@tigon-dms/globals');
        wp_enqueue_script('jquery');
        wp_enqueue_script_module('@tigon-dms/diagnostics');
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
    
    public static function import_script_enqueue()
    {
        $js_url = self::asset_url();
        wp_register_script('@tigon-dms/globals', $js_url . 'globals.js');
        wp_register_script_module('@tigon-dms/base64-js', $js_url . 'node_modules/base64-js/index.js');
        wp_register_script_module('@tigon-dms/ieee754', $js_url . 'node_modules/ieee754/index.js');
        wp_register_script_module('@tigon-dms/buffer', $js_url . 'node_modules/buffer/index.js', ['@tigon-dms/base64-js', '@tigon-dms/ieee754']);
        wp_register_script_module('@tigon-dms/php_serialize', $js_url . 'node_modules/php-serialize/index.js');
        wp_register_script_module('@tigon-dms/import', $js_url . 'import.js', ['jquery', '@tigon-dms/php_serialize']);

        wp_localize_script('@tigon-dms/globals', 'globals', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'siteurl' => get_site_url()
        ]);

        wp_enqueue_script('@tigon-dms/globals');
        wp_enqueue_script_module('@tigon-dms/php_serialize');
        wp_enqueue_script('jquery');
        wp_enqueue_script_module('@tigon-dms/import');
    }

    public static function settings_script_enqueue()
    {
        $js_url = self::asset_url();
        wp_register_script('@tigon-dms/globals', $js_url . 'globals.js');
        wp_register_script_module('@tigon-dms/settings', $js_url . 'settings.js', array('jquery'));

        wp_localize_script('@tigon-dms/globals', 'globals', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'siteurl' => get_site_url()
        ]);

        wp_enqueue_script('@tigon-dms/globals');
        wp_enqueue_script('jquery');
        wp_enqueue_script_module('@tigon-dms/settings');
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

        $product_id = is_callable(array($object, 'get_id')) ? $object->get_id() : (!empty($$object->ID) ? $object->ID : null);
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
        register_rest_route('tigon-dms-connect', 'used', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => 'Tigon\DmsConnect\Admin\REST_Routes::push_used_cart'
        ]);
        register_rest_route('tigon-dms-connect', 'used', [
            'methods' => \WP_REST_Server::DELETABLE,
            'callback' => 'Tigon\DmsConnect\Admin\REST_Routes::delete_used_cart'
        ]);

        register_rest_route('tigon-dms-connect', 'new/update', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => 'Tigon\DmsConnect\Admin\REST_Routes::push_new_cart'
        ]);
        register_rest_route('tigon-dms-connect', 'new/pid', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => 'Tigon\DmsConnect\Admin\REST_Routes::id_by_slug'
        ]);
        register_rest_route('tigon-dms-connect', 'showcase', [
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => 'Tigon\DmsConnect\Admin\REST_Routes::set_grid'
        ]);
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
        $used_raw = \Tigon\DmsConnect\Includes\DMS_Connector::request(
            '{"isUsed":true, "isInStock": true, "isInBoneyard": false, "needOnWebsite": true}',
            '/chimera/lookup',
            'POST'
        );
        $used_carts = json_decode($used_raw, true) ?? [];

        // Fetch new carts from DMS
        $new_raw = \Tigon\DmsConnect\Includes\DMS_Connector::request(
            '{"isUsed":false, "needOnWebsite": true, "isInStock": true, "isInBoneyard": false}',
            '/chimera/lookup',
            'POST'
        );
        $new_carts = json_decode($new_raw, true) ?? [];

        // --- Sync used carts ---
        foreach ($used_carts as $cart) {
            $stats['total']++;
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

        wp_send_json_success($stats);
    }

    // Deactivation Hook
    public static function deactivate()
    {
    }

    // Uninstall Hook
    public static function uninstall()
    {
    }
}
