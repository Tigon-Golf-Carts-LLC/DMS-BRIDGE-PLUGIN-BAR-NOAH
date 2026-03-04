<?php

namespace Tigon\DmsConnect\Abstracts;

use ErrorException;
use Tigon\DmsConnect\Includes\DMS_Connector;
use Tigon\DmsConnect\Includes\Product_Fields;
use Tigon\DmsConnect\Admin\Database_Object;
use Tigon\DmsConnect\Admin\Database_Write_Controller;
use WP_Error;

abstract class Abstract_Import_Controller
{
    private function __construct()
    {
    }

    /**
     * Import single cart via Create Item
     * Cart must already exist
     * @return bool|string
     */
    public static function import_create(Database_Object $data)
    {
        $response = Database_Write_Controller::create_from_database_object($data);
        if (is_wp_error($response)) {
            return $response;
        }

        return json_encode($response);
    }

    /**
     * Import single cart via Update Item
     * Cart must already exist
     * @return bool|string
     */
    public static function import_update(Database_Object $data)
    {
        $response = Database_Write_Controller::update_from_database_object($data);
        if (is_wp_error($response)) {
            return $response;
        }

        return json_encode($response);
    }

    /**
     * Delete single cart via Delete Item
     * Cart must already exist
     * @return String|\WP_Error
     */
    public static function import_delete(Database_Object $data)
    {
        // update_item returns associative array, client wants JSON
        $response_json = json_encode(Database_Write_Controller::delete_by_id($data));

        return $response_json;
    }

    public static function replace_new($data)
    {
        if (!$data['isInStock'] || $data['isInBoneyard']) {
            $dms_filter = '{
                "make": "' . $data['cartType']['make'] . '",
                "model": "' . $data['cartType']['model'] . '",
                "year": ' . $data['cartType']['year'] . ',
                "cartColor": "' . $data['cartAttributes']['cartColor'] . '",
                "seatColor": "' . $data['cartAttributes']['seatColor'] . '",
                "locationId": "' . $data['cartLocation']['locationId'] . '",
                "isInStock":true,
                "isUsed":false,
                "isInBoneyard":false
            }';
            $dms_carts = json_decode(\Tigon\DmsConnect\Includes\DMS_Connector::request($dms_filter, '/chimera/lookup', 'POST'), true);
            if (count($dms_carts ?? []) > 0) {
                $data = $dms_carts[0];
            }
        }


        $new_cart = new \Tigon\DmsConnect\Admin\New\Cart($data);

        $converted_cart = $new_cart->convert(SKU | PRICE | SALE_PRICE | IN_STOCK | MONRONEY_STICKER);

        // Get old monroney ID
        $monroney_url = explode('"', get_post_meta($data['pid'])['monroney_sticker'][0])[1];
        $monroney = attachment_url_to_postid($monroney_url);
        
        $result = Database_Write_Controller::update_from_database_object($converted_cart);

        if (is_wp_error($result)) {
            return $result;
        } else {
            wp_delete_post($monroney, false);
            return new \WP_REST_Response($result, 200);
        }
    }

    /**
     * Gets a given new DMS cart's associated PID, creating it if necessary
     *
     * @param Array $data Raw DMS cart data as an associative array
     * @param int $forced_fields [optional] The fields which should be overridden, using the Attributes::Fields Enum,
     * combined via logical OR. Default 0.
     * @return Array|WP_Error
     */
    public static function import_new($data, int $forced_fields = 0)
    {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $pid = 0;
        $old_pid = null;
        $update_url = null;
        $dms_selector = null;

        Product_Fields::define_constants();

        // This cart is not valid, try to get another
        if (!$data['isInStock'] || $data['isInBoneyard']) {
            $dms_filter = '{
                "make": "' . $data['cartType']['make'] . '",
                "model": "' . $data['cartType']['model'] . '",
                "year": "' . $data['cartType']['year'] . '",
                "cartColor": "' . $data['cartAttributes']['cartColor'] . '",
                "seatColor": "' . $data['cartAttributes']['seatColor'] . '",
                "locationId": "' . $data['cartLocation']['locationId'] . '",
                "isUsed":false,
                "isInStock":true,
                "isInBoneyard":false
            }';
            $dms_result = json_decode(\Tigon\DmsConnect\Includes\DMS_Connector::request($dms_filter, '/chimera/lookup', 'POST'), true) ?? [];
            $valid_replacement = null;
            foreach ($dms_result as $cart) {
                if ($cart['_id'] != $data['_id']) {
                    $valid_replacement = $cart;
                    break;
                }
            }
            if (!$valid_replacement) {
                // If we cant, redirect to replace_new
                return self::replace_new($data);
            } else $data = $valid_replacement;
        }

        // Generate possible slugs
        $location_id = $data['cartLocation']['locationId'];
        if (!isset(\Tigon\DmsConnect\Admin\Attributes::$locations[$location_id])) {
            return new \WP_Error(400, ['pid' => 0, 'error' => "Unknown location: $location_id"]);
        }
        $loc = \Tigon\DmsConnect\Admin\Attributes::$locations[$location_id];
        $city = $loc['city_short'] ?? $loc['city'];

        $make = preg_replace('/\s+/', '-', trim(preg_replace('/\+/', ' plus ', $data['cartType']['make'])));
        $model = preg_replace('/\s+/', '-', trim(preg_replace('/\+/', ' plus ', $data['cartType']['model'])));
        $color = preg_replace('/\s+/', '-', $data['cartAttributes']['cartColor']);
        $seat = preg_replace('/\s+/', '-', $data['cartAttributes']['seatColor']);
        $location = preg_replace('/\s+/', '-', $city . "-" . $loc['st']);
        $year = preg_replace('/\s+/', '-', $data['cartType']['year']);

        $base_slug = strtolower("$make-$model-$color-seat-$seat-$location");
        $year_slug = $base_slug . "-$year";

        // Consistency check for DMS websiteUrl
        if($data['advertising']['websiteUrl'] != site_url() . "/inventory/$base_slug"
            && $data['advertising']['websiteUrl'] != site_url() . "/inventory/$year_slug") {
                $data['advertising']['websiteUrl'] = site_url() . "/inventory/$base_slug";
                $dms_filter = '{
                    "make": "' . $data['cartType']['make'] . '",
                    "model": "' . $data['cartType']['model'] . '",
                    "year": "' . $data['cartType']['year'] . '",
                    "cartColor": "' . $data['cartAttributes']['cartColor'] . '",
                    "seatColor": "' . $data['cartAttributes']['seatColor'] . '",
                    "locationId": "' . $data['cartLocation']['locationId'] . '",
                    "isUsed":false
                }';
                $dms_result = json_decode(\Tigon\DmsConnect\Includes\DMS_Connector::request($dms_filter, '/chimera/lookup', 'POST'), true) ?? [];
                $update_query = array_map(function ($cart) use ($base_slug) {
                    return [
                        '_id' => $cart['_id'],
                        'advertising' => [
                            'websiteUrl' => site_url() . "/inventory/$base_slug"
                        ]
                    ];
                }, $dms_result);
                $update_query = json_encode($update_query);
                DMS_Connector::request($update_query, '/chimera/carts', 'PUT');

        }

        // Get pid if it exists
        $base_pid = intval(get_page_by_path($base_slug, OBJECT, 'product')?->ID) ?? 0;
        $year_pid = intval(get_page_by_path($year_slug, OBJECT, 'product')?->ID) ?? 0;

        // Get years
        $req_year = intval($data['cartType']['year']);
        $year_of_vehicle_attr = get_the_terms($base_pid, 'pa_year-of-vehicle');
        if ($year_of_vehicle_attr != false) {
            $base_year = intval(end($year_of_vehicle_attr)?->name) ?? $req_year;
        } else $base_year = $req_year;


        // If there is an existing separate page for the request year
        if ($base_pid && $year_pid) {
            // Ensure the newest page points to an existing model
            for ($inspected_year = max($base_year, $req_year); $inspected_year >= min($base_year, $req_year); $inspected_year--) {
                // Check if there is a page here
                if ($inspected_year == $base_year) {
                    $inspected_pid = get_page_by_path($base_slug, OBJECT, 'product')?->ID ?? 0;
                } else $inspected_pid = get_page_by_path($base_slug . "-$inspected_year", OBJECT, 'product')?->ID ?? 0;

                if ($inspected_pid) {
                    // Check for carts at each year
                    $dms_filter = '{
                        "make": "' . $data['cartType']['make'] . '",
                        "model": "' . $data['cartType']['model'] . '",
                        "year": "' . $inspected_year . '",
                        "cartColor": "' . $data['cartAttributes']['cartColor'] . '",
                        "seatColor": "' . $data['cartAttributes']['seatColor'] . '",
                        "locationId": "' . $data['cartLocation']['locationId'] . '",
                        "isUsed":false
                    }';
                    $dms_result = json_decode(\Tigon\DmsConnect\Includes\DMS_Connector::request($dms_filter, '/chimera/lookup', 'POST'), true) ?? [];
                    $dms_count = count($dms_result);

                    // Carts were found at this year
                    if ($dms_count > 0) {
                        // Set found page to be base if necessary
                        if ($inspected_year != $base_year) {
                            // In case base was older than request
                            if ($inspected_year > $base_year) {
                                // Append year
                                $wpdb->update(
                                    $wpdb->prefix . 'posts',
                                    ['post_name' => $base_slug . "-$base_year"],
                                    ['ID' => $base_pid]
                                );
                                // Update DMS
                                $dms_filter = '{
                                    "make": "' . $data['cartType']['make'] . '",
                                    "model": "' . $data['cartType']['model'] . '",
                                    "year": "' . $base_year . '",
                                    "cartColor": "' . $data['cartAttributes']['cartColor'] . '",
                                    "seatColor": "' . $data['cartAttributes']['seatColor'] . '",
                                    "locationId": "' . $data['cartLocation']['locationId'] . '",
                                    "isUsed":false
                                }';
                                $dms_result = json_decode(\Tigon\DmsConnect\Includes\DMS_Connector::request($dms_filter, '/chimera/lookup', 'POST'), true) ?? [];
                                $update_query = array_map(function ($cart) use ($base_pid, $base_slug, $base_year) {
                                    return [
                                        '_id' => $cart['_id'],
                                        'advertising' => [
                                            'websiteUrl' => "$base_slug-$base_year"
                                        ],
                                        'pid' => "$base_pid"
                                    ];
                                }, $dms_result);
                                $update_query = json_encode($update_query);
                                DMS_Connector::request($update_query, '/chimera/carts', 'PUT');
                            }

                            // Inspected is now newest
                            $wpdb->update(
                                $wpdb->prefix . 'posts',
                                ['post_name' => $base_slug],
                                ['ID' => $inspected_pid]
                            );
                            $pid = $year_pid;
                            $old_pid = $inspected_pid;
                            $update_url = get_site_url() . "/inventory/$base_slug";
                            $dms_selector = '{
                                "make": "' . $data['cartType']['make'] . '",
                                "model": "' . $data['cartType']['model'] . '",
                                "year": "' . $inspected_year . '",
                                "cartColor": "' . $data['cartAttributes']['cartColor'] . '",
                                "seatColor": "' . $data['cartAttributes']['seatColor'] . '",
                                "locationId": "' . $data['cartLocation']['locationId'] . '",
                                "isUsed":false
                            }';
                        } else {
                            if($req_year === $base_year) {
                                $pid = $base_pid;
                                if($data['advertising']['websiteUrl'] != site_url() . "/inventory/$base_slug") {
                                    $data['advertising']['websiteUrl'] = site_url() . "/inventory/$base_slug";
                                    $old_pid = &$pid;
                                    $update_url = site_url() . "/inventory/$base_slug";
                                    $dms_selector = '{
                                        "make": "' . $data['cartType']['make'] . '",
                                        "model": "' . $data['cartType']['model'] . '",
                                        "year": "' . $req_year . '",
                                        "cartColor": "' . $data['cartAttributes']['cartColor'] . '",
                                        "seatColor": "' . $data['cartAttributes']['seatColor'] . '",
                                        "locationId": "' . $data['cartLocation']['locationId'] . '",
                                        "isUsed":false
                                    }';
                                }
                            } else {
                                $pid = $year_pid;
                                if($data['advertising']['websiteUrl'] != site_url() . "/inventory/$base_slug-$req_year") {
                                    $data['advertising']['websiteUrl'] = site_url() . "/inventory/$base_slug-$req_year";
                                    $old_pid = &$pid;
                                    $update_url = site_url() . "/inventory/$base_slug-$req_year";
                                    $dms_selector = '{
                                        "make": "' . $data['cartType']['make'] . '",
                                        "model": "' . $data['cartType']['model'] . '",
                                        "year": "' . $req_year . '",
                                        "cartColor": "' . $data['cartAttributes']['cartColor'] . '",
                                        "seatColor": "' . $data['cartAttributes']['seatColor'] . '",
                                        "locationId": "' . $data['cartLocation']['locationId'] . '",
                                        "isUsed":false
                                    }';
                                }
                            }
                        }
                        break;
                    }

                    // No carts were found at this year, It cannot be the newest
                    if ($dms_count == 0) {
                        $wpdb->update(
                            $wpdb->prefix . 'posts',
                            ['post_name' => $base_slug . "-$inspected_year"],
                            ['ID' => $inspected_pid]
                        );
                        // Set visibility (via stock)
                        $wpdb->update(
                            $wpdb->prefix . 'postmeta',
                            ['meta_value' => 'outofstock'],
                            ['post_id' => $inspected_pid, 'meta_key' => '_stock_status']
                        );
                    }
                }
            }
            // Else if request is newer
        } else if ($req_year > $base_year) {
            $pid = 0;
            $data['pid'] = null;
            $data['advertising']['websiteUrl'] = site_url() . "/inventory/$base_slug";
            $old_pid = $base_pid;
            $update_url = site_url() . "/inventory/$base_slug-$base_year";
            $dms_selector = '{
                "make": "' . $data['cartType']['make'] . '",
                "model": "' . $data['cartType']['model'] . '",
                "year": "' . $base_year . '",
                "cartColor": "' . $data['cartAttributes']['cartColor'] . '",
                "seatColor": "' . $data['cartAttributes']['seatColor'] . '",
                "locationId": "' . $data['cartLocation']['locationId'] . '",
                "isUsed":false
            }';

            $sql_result = $wpdb->update(
                $wpdb->prefix . 'posts',
                ['post_name' => "$base_slug-$base_year"],
                ['ID' => $base_pid]
            );
            // Else if request is older
        } else if ($req_year < $base_year) {
            $pid = 0;
            $data['pid'] = null;
            $data['advertising']['websiteUrl'] = site_url() . "/inventory/$base_slug-$req_year";
            $old_pid = &$pid;
            $update_url = site_url() . "/inventory/$base_slug-$req_year";
            $dms_selector = '{
                "make": "' . $data['cartType']['make'] . '",
                "model": "' . $data['cartType']['model'] . '",
                "year": "' . $req_year . '",
                "cartColor": "' . $data['cartAttributes']['cartColor'] . '",
                "seatColor": "' . $data['cartAttributes']['seatColor'] . '",
                "locationId": "' . $data['cartLocation']['locationId'] . '",
                "isUsed":false
            }';
            // Otherwise it's newest
        } else {
            $pid = $base_pid;
        }

        // Existing page operations
        if ($pid != 0) {
            $product = wc_get_product($pid);
            $post_sku = $product->get_sku();

            if ($post_sku === $data['vinNo'] || $post_sku === $data['serialNo']) {
                $forced_fields = (ALL_FIELDS & ~PUBLISHED) | ($forced_fields & PUBLISHED);
            }

            // Check if the page currently contains a valid cart
            $srl_raw = \Tigon\DmsConnect\Includes\DMS_Connector::request(
                '{"serialNo":"' . $post_sku . '","isInBoneyard":false,"isInStock":true}',
                '/chimera/lookup',
                'POST'
            );
            $srl_decoded = $srl_raw ? json_decode($srl_raw, true) : [];
            $srl_exists = is_array($srl_decoded) && count($srl_decoded) > 0;

            $vin_raw = \Tigon\DmsConnect\Includes\DMS_Connector::request(
                '{"vinNo":"' . $post_sku . '","isInBoneyard":false,"isInStock":true}',
                '/chimera/lookup',
                'POST'
            );
            $vin_decoded = $vin_raw ? json_decode($vin_raw, true) : [];
            $vin_exists = is_array($vin_decoded) && count($vin_decoded) > 0;

            // If it is invalid, replace it
            if (($srl_exists || $vin_exists) === false || substr($post_sku, 0, 5) === 'TIGON') {
                $data['pid'] = $pid;
                $forced_fields = $forced_fields | SKU | MONRONEY_STICKER | IN_STOCK;
            }
        }

        // Resolve possible sku conflict
        $req_sku = $data['vinNo'] ?? $data['serialNo'];
        $conflict_pid = wc_get_product_id_by_sku($req_sku) ?? 0;
        if ($conflict_pid && $conflict_pid != $pid) {
            // Check if there is another cart at the conflict pid
            $dms_result = json_decode(\Tigon\DmsConnect\Includes\DMS_Connector::request('{"pid": "' . $conflict_pid . '"}', '/chimera/lookup', 'POST'), true) ?? [];
            $conflict_replacement = null;
            foreach ($dms_result as $cart) {
                if ($cart['_id'] != $data['_id']) {
                    $conflict_replacement = $cart;
                    break;
                }
            }
            if ($conflict_replacement) {
                // If there is, initiate replacement
                self::replace_new($conflict_replacement);
            } else {
                // TODO This routine could leave a base url as a 404
                // If there isnt one, set conflict to no sku and out of stock
                $wpdb->update(
                    $wpdb->prefix . 'postmeta',
                    ['meta_value' => 'outofstock'],
                    ['post_id' => $conflict_pid, 'meta_key' => '_stock_status']
                );
                $wpdb->update(
                    $wpdb->prefix . 'postmeta',
                    ['meta_value' => 'TIGON' . time()],
                    ['post_id' => $conflict_pid, 'meta_key' => '_sku']
                );
            }
        }

        // Create page if doesn't exist or if it needs a force update
        if (!$pid || $forced_fields) {
            $new_carts = new \Tigon\DmsConnect\Admin\New\New_Cart_Converter();

            // Fill empty data with defaults if available
            $cart_defaults = $new_carts->get_specific(preg_replace('/\+$/', ' Plus', $data['cartType']['model']));
            if (is_wp_error($cart_defaults)) $cart_defaults = [];

            $cart = array_replace_recursive($cart_defaults, $data);
            $cart['advertising']['cartAddOns'] = ['Standard Add Ons'];
            $cart['pid'] = $pid;

            $new_cart = new \Tigon\DmsConnect\Admin\New\Cart($cart);

            $converted = $new_cart->convert($pid ? $forced_fields : ALL_FIELDS);

            if ($converted->get_value('method') == 'create' && $converted) {
                $result = self::import_create($converted);
                $message = 'Cart has been created';
            } else if ($converted->get_value('method') == 'update' && $converted) {
                $result = self::import_update($converted);
                $message = 'Cart has been force updated';
            } else {
                return new \WP_Error(500, ['pid' => 0, 'error' => 'No cart has been imported'], $converted);
            }

            if (is_wp_error($result)) {
                return new \WP_Error(500, ['pid' => 0, 'error' => 'Import failure', $result]);
            }
            if ($result === null) return new \WP_REST_Response('Result was actually null', 500);
            $result = json_decode($result, true);
            $pid = $result['pid'];
        }
        return ['pid' => $pid, 'oldPid' => $old_pid, 'updateUrl' => $update_url, 'dmsSelector' => $dms_selector];
    }

    /**
     * Place tasks to be run after imports here. This will only be run once during ajax updates
     *
     * @return void
     */
    public static function process_post_import() {
        wc_update_product_lookup_tables();

        // Clear DMS API caches so frontend serves fresh data
        if (class_exists('DMS_API')) {
            \DMS_API::clear_caches();
        }
    }
}
