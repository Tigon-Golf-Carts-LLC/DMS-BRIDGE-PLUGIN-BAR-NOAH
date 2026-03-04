<?php

namespace Tigon\DmsConnect\Admin;

use Tigon\DmsConnect\Includes\Product_Fields;
use Tigon\DmsConnect\Admin\Database_Write_Controller;

abstract class Ajax_Import_Controller extends \Tigon\DmsConnect\Abstracts\Abstract_Import_Controller
{
    private function __construct()
    {
    }

    /**
     * Ajax wrapper for get_db
     * @return never
     */
    public static function query_dms()
    {
        check_ajax_referer('tigon_dms_run_import_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header("Content-Type: application/json; charset=utf-8", true);

            // Data from AJAX request
            // AJAX produces unwanted slashes
            $endpoint = stripcslashes($_REQUEST['endpoint']);
            $query = stripcslashes($_REQUEST['query']);

            echo \Tigon\DmsConnect\Includes\DMS_Connector::request($query, $endpoint, 'POST');
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        exit;
    }

    /**
     * Ajax function to convert a single cart to WP Format
     * @return never
     */
    public static function ajax_import_convert()
    {
        check_ajax_referer('tigon_dms_run_import_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header("Content-Type: application/json; charset=utf-8", true);

            // Data from AJAX request
            // AJAX produces unwanted slashes
            $stripped = stripcslashes($_REQUEST['data']);

            // convert_to_import accepts associative array
            $a_array = json_decode($stripped, true);

            

            if ($a_array['pid']) {
                $product = wc_get_product($a_array['pid']);
                if($product===false) {
                    $request['pid'] = wc_get_product_id_by_sku($a_array['vinNo']?$a_array['vinNo']:$a_array['serialNo']);
                    $product = wc_get_product(
                        $a_array['pid']
                    );
                }

                if($product) {
                    $featured_image = get_post_thumbnail_id($a_array['pid']);
                    wp_delete_post($featured_image, true);

                    $images = $product?->get_gallery_image_ids()??[];
                    foreach ($images as $i) {
                        wp_delete_post($i, true);
                    }

                    $monroney_url = explode('"', get_post_meta($a_array['pid'])['monroney_sticker'][0])[1];
                    $monroney = attachment_url_to_postid($monroney_url);
                    wp_delete_post($monroney, true);
                } else $a_array['pid'] = null;
            }

            $used_cart = new \Tigon\DmsConnect\Admin\Used\Cart($a_array);

            $data = $used_cart->convert();

            echo json_encode(['data' => serialize($data)]);
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        exit;
    }

    /**
     * Ajax function to convert a single cart to WP Format
     * @return never
     */
    public static function ajax_new_import_convert()
    {
        check_ajax_referer('tigon_dms_run_import_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header("Content-Type: application/json; charset=utf-8", true);

            // Data from AJAX request
            // AJAX produces unwanted slashes
            $stripped = stripcslashes($_REQUEST['data']);

            // convert_to_import accepts associative array
            $a_array = json_decode($stripped, true);

            $new_cart = new \Tigon\DmsConnect\Admin\New\Cart($a_array);

            $data = $new_cart->convert();

            echo json_encode($data);
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        exit;
    }

    /**
     * Import single cart via Create Item
     * Cart must already exist
     * @return bool|string
     */
    public static function import_create(Database_Object $data)
    {
        // create_item returns associative array, client wants JSON
        return json_encode(Database_Write_Controller::create_from_database_object($data));
    }
    // Ajax wrapper for above
    public static function ajax_import_create()
    {
        check_ajax_referer('tigon_dms_run_import_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header("Content-Type: application/json; charset=utf-8", true);

            // Data from AJAX request
            // AJAX produces unwanted slashes
            $data = stripcslashes($_REQUEST['data']);
            $data = unserialize($data, ['allowed_classes' => [Database_Object::class]]);
            $result = Ajax_Import_Controller::import_create($data);
            echo $result;

            // Update PID on DMS
            $result = json_decode($result, true);

            $pid_request = json_encode([
                [
                    '_id' => $data->get_value('_id'),
                    'pid' => $result['pid'],
                    'advertising' => [
                        'onWebsite' => true,
                        'websiteUrl' => $result['websiteUrl']
                    ]
                ]
            ]);

            \Tigon\DmsConnect\Includes\DMS_Connector::request($pid_request, '/chimera/carts', 'PUT');
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        exit;
    }

    /**
     * Import single cart via Update Item
     * Cart must already exist
     * @return bool|string
     */
    public static function import_update(Database_Object $data)
    {

        // update_item returns associative array, client wants JSON
        return json_encode(\Tigon\DmsConnect\Admin\Database_Write_Controller::update_from_database_object($data));
    }
    // Ajax wrapper for above
    public static function ajax_import_update()
    {
        check_ajax_referer('tigon_dms_run_import_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header("Content-Type: application/json; charset=utf-8", true);

            // Data from AJAX request
            // AJAX produces unwanted slashes
            $data = stripcslashes($_REQUEST['data']);
            $data = unserialize($data, ['allowed_classes' => [Database_Object::class]]);
            $result = Ajax_Import_Controller::import_update($data);
            echo $result;

            // Update PID on DMS
            $result = json_decode($result, true);

            $pid_request = json_encode([
                [
                    '_id' => $data->get_value('_id'),
                    'pid' => $result['pid'],
                    'advertising' => [
                        'onWebsite' => true,
                        'websiteUrl' => $result['websiteUrl']
                    ]
                ]
            ]);

            \Tigon\DmsConnect\Includes\DMS_Connector::request($pid_request, '/chimera/carts', 'PUT');
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        exit;
    }
    
    // Ajax wrapper for above
    public static function ajax_import_delete()
    {
        check_ajax_referer('tigon_dms_run_import_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header("Content-Type: application/json; charset=utf-8", true);

            // Data from AJAX request
            // AJAX produces unwanted slashes
            $data = stripcslashes($_REQUEST['data']);
            $data = unserialize($data, ['allowed_classes' => [Database_Object::class]]);

            echo Ajax_Import_Controller::import_delete($data);
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        exit;
    }

    public static function ajax_import_new()
    {
        check_ajax_referer('tigon_dms_run_import_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header("Content-Type: application/json; charset=utf-8", true);

            // Data from AJAX request
            // AJAX produces unwanted slashes
            $data = stripcslashes($_REQUEST['data']);
            $data = json_decode($data, true);

			$forced_fields = 0;
            if($_REQUEST['forced']??false) {
                $forced_fields = stripcslashes($_REQUEST['forced']);
                $forced_fields = json_decode($forced_fields);
                Product_Fields::define_constants();
                $forced_fields = array_map(function($value) {
                    return constant($value);
                }, $forced_fields);
                $forced_fields = Product_Fields::combine_options(...$forced_fields);
            }

            $result = Ajax_Import_Controller::import_new($data, $forced_fields);
            echo json_encode($result);
            if(is_wp_error($result)) {
                error_log('Tigon DMS Connect error: ' . json_encode($result));
                exit;
            }

            // Update PID on DMS for all similar carts
            $dms_filter = '{
                "make": "' . $data['cartType']['make'] . '",
                "model": "' . $data['cartType']['model'] . '",
                "year": "' . $data['cartType']['year'] . '",
                "cartColor": "' . $data['cartAttributes']['cartColor'] . '",
                "seatColor": "' . $data['cartAttributes']['seatColor'] . '",
                "locationId": "' . $data['cartLocation']['locationId'] . '"
            }';
            $new_pids = \Tigon\DmsConnect\Includes\DMS_Connector::request($dms_filter, '/chimera/lookup', 'POST');
            $new_pids = json_decode($new_pids, true);

            $new_pids = array_map(function($cart) use ($result) {
                $query = '{
                    "_id": "'.$cart['_id'].'",
                    "pid": "'.$result['pid'].'"
                }';
                return $query;
            }, $new_pids);

            \Tigon\DmsConnect\Includes\DMS_Connector::request('['.implode(',', $new_pids).']', '/chimera/carts', 'PUT');

            // Update oldPid if set
            if($result['oldPid']) {
                $update_pids = json_decode(\Tigon\DmsConnect\Includes\DMS_Connector::request($result['dmsSelector'], '/chimera/lookup', 'POST'), true);

                $update_pids = array_map(function($cart) use ($result) {
                    $query = '{
                        "_id": "'.$cart['_id'].'",
                        "pid": "'.$result['oldPid'].'",
                        "advertising": {
                            "websiteUrl": "'.$result['updateUrl'].'"
                        }
                    }';
                    return $query;
                }, $update_pids);

                \Tigon\DmsConnect\Includes\DMS_Connector::request('['.implode(',', $update_pids).']', '/chimera/carts', 'PUT');
            }
        } else {
            header("Location: " . $_SERVER["HTTP_REFERER"]);
        }

        exit;
    }
}
