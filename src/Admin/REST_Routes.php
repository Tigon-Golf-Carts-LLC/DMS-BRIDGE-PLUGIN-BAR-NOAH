<?php

namespace Tigon\DmsConnect\Admin;

use Automattic\WooCommerce\Blocks\Utils\Utils;
use ErrorException;
use Tigon\DmsConnect\Admin\Used\Cart as UsedCart;
use Tigon\DmsConnect\Admin\New\Cart as NewCart;
use Tigon\DmsConnect\Includes\Product_Media;
use Tigon\DmsConnect\Includes\Utilities;
use WP_Error;
use WP_REST_Response;

class REST_Routes
{

    private function __construct()
    {
    }

    /**
     * Resolve a product ID from a request, falling back to SKU lookup.
     *
     * @param array $data Request data with 'pid', 'vinNo', 'serialNo'.
     * @return \WC_Product|null The product, or null if not found.
     */
    private static function resolve_product(array &$data): ?\WC_Product
    {
        if (!empty($data['pid'])) {
            $product = wc_get_product($data['pid']);
            if ($product !== false) {
                return $product;
            }

            // PID didn't resolve — try SKU fallback
            $sku = !empty($data['vinNo']) ? $data['vinNo'] : ($data['serialNo'] ?? '');
            if ($sku) {
                $data['pid'] = wc_get_product_id_by_sku($sku);
                $product = wc_get_product($data['pid']);
                if ($product !== false) {
                    return $product;
                }
            }

            $data['pid'] = null;
        }

        return null;
    }

    /**
     * REST Callback for used CREATABLE
     *
     * @param array|WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public static function push_used_cart($request)
    {
        Product_Media::require_media_functions();
        $processed_request = (get_class($request) == 'WP_REST_Request') ? json_decode($request->get_body(), true) : $request;
        if (isset($processed_request['_id']) || isset($processed_request['pid'])) {
            // If cart is not eligible for website, delete it
            if (
                !$processed_request['isInStock'] ||
                !$processed_request['advertising']['needOnWebsite'] ||
                $processed_request['isInBoneyard']
            ) {
                return self::delete_used_cart($processed_request);
            }

            // Clear existing media before re-import
            $product = self::resolve_product($processed_request);
            if ($product) {
                Product_Media::delete_product_media((int) $processed_request['pid']);
            }

            $used_cart = new UsedCart($processed_request);

            $converted = $used_cart->convert();
            if(is_wp_error($converted)) return $converted;


            if ($converted->get_value('method') == 'create' && $converted) {
                $result = \Tigon\DmsConnect\Admin\REST_Import_Controller::import_create($converted);
                if (is_wp_error($result)) return $result;

                $result = json_decode($result, true);
                $code = 201;
                $message = 'Cart has been created';
            } else if ($converted->get_value('method') == 'update' && $converted) {
                $result = \Tigon\DmsConnect\Admin\REST_Import_Controller::import_update($converted);
                $code = 200;
                $message = 'Cart has been updated';
            } else
                return new \WP_Error(500, ['pid' => 0, 'error' => 'No cart has been imported', $converted->get_value()]);

            if (is_wp_error($result)) {
                return new \WP_Error(500, ['pid' => 0, 'error' => 'Import failure', $converted->get_value()]);
            }

            if(is_string($result))
                $result = json_decode($result, true);
            $result['url'] = get_permalink($result['pid']);

            REST_Import_Controller::process_post_import();
            return new \WP_REST_Response($result, $code);
        } else
            return new \WP_Error(400, 'Bad Request: Body must consist of one and only one Cart Data Object', $request);
    }

    /**
     * REST Callback for used DELETABLE
     *
     * Fully deletes the product and all associated media.
     *
     * @param array|WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public static function delete_used_cart($request)
    {
        Product_Media::require_media_functions();
        if (isset($request['pid']) || isset($request['vinNo']) || isset($request['serialNo'])) {
            $product = self::resolve_product($request);
            if (!$product) {
                return new \WP_REST_Response('No fight left to fight', 410);
            }

            $pid = (int) $request['pid'];

            // Fully delete product and all its media
            Product_Media::delete_product($pid);

            REST_Import_Controller::process_post_import();
            return new \WP_REST_Response([
                'message'     => 'Post ' . $pid . ' deleted successfully',
                'pid'         => $pid,
                'isOnWebsite' => false,
            ], 200);
        } else
            return new \WP_Error(400, 'Bad Request: Body must be of the form {"pid":######}', $request);
    }

    /**
     * REST Callback for new/update CREATABLE
     *
     * @param array|WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public static function push_new_cart($request)
    {
        Product_Media::require_media_functions();
        $processed_request = (is_object($request) && get_class($request) == 'WP_REST_Request') ? json_decode($request->get_body(), true) : $request;
        if (isset($processed_request['_id']) && isset($processed_request['pid']) && (isset($processed_request['vinNo']) || isset($processed_request['serialNo']))) {
            $result = \Tigon\DmsConnect\Admin\REST_Import_Controller::replace_new($processed_request);
            REST_Import_Controller::process_post_import();
            return $result;
        } else
            return new \WP_Error(400, 'Bad Request: Body must contain _id, pid, and one or both of vinNo and serialNo (vinNo preferred)', json_encode($processed_request));
    }

    /**
     * REST Callback for new/pid CREATABLE
     *
     * @param array|WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public static function id_by_slug($request)
    {
        $processed_request = (is_object($request) && get_class($request) == 'WP_REST_Request') ? json_decode($request->get_body(), true) : $request;
        if (isset($request['advertising']['websiteUrl'])) {
            $result = \Tigon\DmsConnect\Admin\REST_Import_Controller::import_new($processed_request);
            REST_Import_Controller::process_post_import();
            return $result;
        } else
            return new \WP_Error(400, 'Bad Request: Body must contain {"advertising": {"websiteUrl": ********} }', $request);
    }

    // ─────────────────────────────────────────────────────────────
    //  Single-Cart Instant Push — DMS → WooCommerce
    //
    //  Accepts one cart payload from the DMS when a cart is updated
    //  or changed.  Runs it through the full import pipeline
    //  (images, categories, attributes, schema templates, AND
    //  user-configured field mappings) then returns the resulting
    //  WooCommerce product ID and permalink.
    //
    //  POST /wp-json/tigon-dms-connect/v1/push
    //  Body: a single DMS cart object (JSON)
    // ─────────────────────────────────────────────────────────────

    /**
     * REST Callback — single cart instant push / update from DMS.
     *
     * @param \WP_REST_Request|array $request
     * @return WP_REST_Response|WP_Error
     */
    public static function push_single_cart($request)
    {
        Product_Media::require_media_functions();

        $cart = (is_object($request) && get_class($request) === 'WP_REST_Request')
            ? json_decode($request->get_body(), true)
            : $request;

        // ── Validate: need at least an _id ─────────────────────────
        if (empty($cart) || !is_array($cart)) {
            return new \WP_Error(
                'bad_request',
                'Request body must be a JSON object representing a single DMS cart.',
                ['status' => 400]
            );
        }
        if (!isset($cart['_id']) && !isset($cart['pid'])) {
            return new \WP_Error(
                'missing_id',
                'Cart payload must contain at least an _id or pid field.',
                ['status' => 400]
            );
        }

        try {
            // Ensure constants are loaded for the import pipeline
            \Tigon\DmsConnect\Includes\Product_Fields::define_constants();

            // Determine cart type: used or new
            $is_used = !empty($cart['isUsed']);

            if ($is_used) {
                // ── Used-cart path (same pipeline as POST /used) ────
                $used_cart = new UsedCart($cart);
                $converted = $used_cart->convert();

                if (is_wp_error($converted)) {
                    return $converted;
                }

                if ($converted->get_value('method') === 'create') {
                    $result = REST_Import_Controller::import_create($converted);
                    $code   = 201;
                } else {
                    $result = REST_Import_Controller::import_update($converted);
                    $code   = 200;
                }
            } else {
                // ── New-cart path (Abstract_Import_Controller) ──────
                $result = \Tigon\DmsConnect\Abstracts\Abstract_Import_Controller::import_new($cart, 0);

                if (is_wp_error($result)) {
                    return $result;
                }

                // import_new returns an array, not JSON
                $pid  = $result['pid'] ?? 0;
                $code = $pid ? 200 : 201;

                // Apply user-configured field mappings on top
                if ($pid && function_exists('tigon_dms_apply_custom_mappings')) {
                    tigon_dms_apply_custom_mappings($pid, $cart);
                }

                REST_Import_Controller::process_post_import();

                return new WP_REST_Response([
                    'success' => true,
                    'pid'     => $pid,
                    'url'     => $pid ? get_permalink($pid) : null,
                    'action'  => $code === 201 ? 'created' : 'updated',
                ], $code);
            }

            // ── Handle result for used-cart path ───────────────────
            if (is_wp_error($result)) {
                return new \WP_Error('import_failed', 'Import failed', ['status' => 500]);
            }

            if (is_string($result)) {
                $result = json_decode($result, true);
            }

            $pid = $result['pid'] ?? 0;

            // Apply user-configured field mappings on top
            if ($pid && function_exists('tigon_dms_apply_custom_mappings')) {
                tigon_dms_apply_custom_mappings($pid, $cart);
            }

            REST_Import_Controller::process_post_import();

            return new WP_REST_Response([
                'success' => true,
                'pid'     => $pid,
                'url'     => $pid ? get_permalink($pid) : null,
                'action'  => $code === 201 ? 'created' : 'updated',
            ], $code);
        } catch (\Throwable $e) {
            return new \WP_Error(
                'push_error',
                $e->getMessage(),
                ['status' => 500]
            );
        }
    }

    /**
     * REST Callback for showcase CREATABLE
     *
     * @param array|WP_REST_Request $request
     * @return WP_REST_Response|WP_Error
     */
    public static function set_grid($request)
    {
        $processed_request = (is_object($request) && get_class($request) == 'WP_REST_Request') ? json_decode($request->get_body(), true) : $request;
        if (isset($processed_request['data']) && isset($processed_request['key'])) {
            $locations = tigon_dms_get_showcase_locations();
            $key = $processed_request['key'];

            if (!isset($locations[$key])) {
                return new \WP_Error('Bad Request', 'Invalid showcase name', $key);
            }

            $loc = $locations[$key];
            $landing_page   = (int) ($loc['landing_page'] ?? 0);
            $archive        = (int) ($loc['archive'] ?? 0);
            $archive_not_in = array_map('intval', $loc['archive_not_in'] ?? []);

            $result = \Tigon\DmsConnect\Admin\REST_Product_Grid_Controller::set(
                landing_page: $landing_page,
                archive: $archive,
                data: $processed_request['data'],
                archive_not_in: $archive_not_in,
                location_name: $processed_request['key']
            );
            return new \WP_REST_Response($result, 200);
        } else
            return new \WP_Error(400, 'Bad Request: Body must contain a list of pids and the target product grid (new or used)', $processed_request);
    }
}
