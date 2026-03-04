<?php

namespace Tigon\DmsConnect\Admin;

use Tigon\DmsConnect\Includes\Product_Fields;
use Tigon\DmsConnect\Includes\Product_Media;
use Tigon\DmsConnect\Admin\Database_Write_Controller;

abstract class Ajax_Import_Controller extends \Tigon\DmsConnect\Abstracts\Abstract_Import_Controller
{
    private function __construct()
    {
    }

    /**
     * Common AJAX guard: verify nonce, capability, and XHR.
     *
     * @return bool True if request is valid AJAX, false otherwise (sends redirect).
     */
    private static function validate_ajax_request(): bool
    {
        check_ajax_referer('tigon_dms_run_import_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized', 403);
        }

        ignore_user_abort(true);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header("Content-Type: application/json; charset=utf-8", true);
            return true;
        }

        header("Location: " . $_SERVER["HTTP_REFERER"]);
        return false;
    }

    /**
     * Ajax wrapper for get_db
     * @return never
     */
    public static function query_dms()
    {
        if (!self::validate_ajax_request()) {
            exit;
        }

        // Data from AJAX request
        // AJAX produces unwanted slashes
        $endpoint = stripcslashes($_REQUEST['endpoint']);
        $query = stripcslashes($_REQUEST['query']);

        echo \Tigon\DmsConnect\Includes\DMS_Connector::request($query, $endpoint, 'POST');

        exit;
    }

    /**
     * Ajax function to convert a used cart to WP Format.
     *
     * Clears existing media before conversion so fresh images are attached.
     *
     * @return never
     */
    public static function ajax_import_convert()
    {
        if (!self::validate_ajax_request()) {
            exit;
        }

        $stripped = stripcslashes($_REQUEST['data']);
        $a_array = json_decode($stripped, true);

        // Clear existing media before re-import
        if (!empty($a_array['pid'])) {
            Product_Media::require_media_functions();
            $product = wc_get_product($a_array['pid']);
            if ($product === false) {
                $a_array['pid'] = wc_get_product_id_by_sku(!empty($a_array['vinNo']) ? $a_array['vinNo'] : ($a_array['serialNo'] ?? ''));
                $product = wc_get_product($a_array['pid']);
            }

            if ($product) {
                Product_Media::delete_product_media((int) $a_array['pid']);
            } else {
                $a_array['pid'] = null;
            }
        }

        $used_cart = new \Tigon\DmsConnect\Admin\Used\Cart($a_array);
        $data = $used_cart->convert();

        echo json_encode(['data' => serialize($data)]);
        exit;
    }

    /**
     * Ajax function to convert a new cart to WP Format.
     *
     * @return never
     */
    public static function ajax_new_import_convert()
    {
        if (!self::validate_ajax_request()) {
            exit;
        }

        $stripped = stripcslashes($_REQUEST['data']);
        $a_array = json_decode($stripped, true);

        $new_cart = new \Tigon\DmsConnect\Admin\New\Cart($a_array);
        $data = $new_cart->convert();

        echo json_encode($data);
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

    /**
     * Common AJAX handler for import create/update operations.
     *
     * Both ajax_import_create and ajax_import_update follow the same pattern:
     * deserialize data, call the import method, echo result, then update DMS PIDs.
     *
     * @param callable $import_fn The import function to call (import_create or import_update).
     * @return never
     */
    private static function ajax_import_write(callable $import_fn): void
    {
        if (!self::validate_ajax_request()) {
            exit;
        }

        $data = stripcslashes($_REQUEST['data']);
        $data = unserialize($data, ['allowed_classes' => [Database_Object::class]]);
        $result = $import_fn($data);
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

        exit;
    }

    /**
     * Ajax wrapper for import_create
     * @return never
     */
    public static function ajax_import_create()
    {
        self::ajax_import_write([self::class, 'import_create']);
    }

    /**
     * Ajax wrapper for import_update
     * @return never
     */
    public static function ajax_import_update()
    {
        self::ajax_import_write([self::class, 'import_update']);
    }

    /**
     * Ajax wrapper for import_delete
     * @return never
     */
    public static function ajax_import_delete()
    {
        if (!self::validate_ajax_request()) {
            exit;
        }

        $data = stripcslashes($_REQUEST['data']);
        $data = unserialize($data, ['allowed_classes' => [Database_Object::class]]);

        echo Ajax_Import_Controller::import_delete($data);

        exit;
    }

    public static function ajax_import_new()
    {
        if (!self::validate_ajax_request()) {
            exit;
        }

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

        exit;
    }
}
