<?php

namespace Tigon\DmsConnect\Includes;

use Tigon\DmsConnect\Admin\Attributes;

/**
 * Determines whether a DMS cart has all required mappings to be published.
 *
 * Required fields: SKU, Price, Categories (make), Location, Manufacturers,
 * Models, Vehicle Class, Drivetrain, Inventory Status, Brands.
 *
 * Products missing any required mapping are set to "draft" until a subsequent
 * sync provides the missing data.  Products with all mappings but no images
 * are still published.
 */
class Product_Readiness
{
    /**
     * Validate cart data and return 'publish' or 'draft'.
     *
     * @param array $cart_data Full DMS cart payload.
     * @return array {
     *     @type string   $status  'publish' or 'draft'
     *     @type string[] $missing Human-readable list of missing fields (empty when status is 'publish')
     * }
     */
    public static function evaluate(array $cart_data): array
    {
        $missing = [];

        // 1. SKU — needs vinNo, serialNo, or enough data to generate a fallback
        if (!self::has_sku($cart_data)) {
            $missing[] = 'SKU (no vinNo, serialNo, or sufficient make/model/color data)';
        }

        // 2. Price — retailPrice must be a positive number
        $price = $cart_data['retailPrice'] ?? null;
        if ($price === null || $price === '' || floatval($price) <= 0) {
            $missing[] = 'Price (retailPrice is empty or zero)';
        }

        // 3. Categories — at minimum the make must be present
        $make = $cart_data['cartType']['make'] ?? '';
        if (empty(trim($make))) {
            $missing[] = 'Categories (cartType.make is empty)';
        }

        // 4. Location — locationId must resolve to a known store
        $location_id = $cart_data['cartLocation']['locationId'] ?? '';
        if (!self::has_location($location_id)) {
            $missing[] = 'Location (cartLocation.locationId "' . $location_id . '" does not resolve)';
        }

        // 5. Manufacturers — derived from make; make must be non-empty
        if (empty(trim($make))) {
            // Already flagged under Categories, but flag separately for clarity
            if (!in_array('Categories (cartType.make is empty)', $missing)) {
                $missing[] = 'Manufacturers (cartType.make is empty)';
            }
        }

        // 6. Models — needs model name for taxonomy assignment
        $model = $cart_data['cartType']['model'] ?? '';
        if (empty(trim($model))) {
            $missing[] = 'Models (cartType.model is empty)';
        }

        // 7. Vehicle Class — always derivable when we have basic cart data,
        //    but model is needed for a meaningful classification
        if (empty(trim($model))) {
            if (!in_array('Models (cartType.model is empty)', $missing)) {
                $missing[] = 'Vehicle Class (cartType.model is empty)';
            }
        }

        // 8. Drivetrain — derived from cartAttributes.driveTrain, defaults to 2X4
        //    Always derivable, but flag if cartAttributes is entirely missing
        if (!isset($cart_data['cartAttributes'])) {
            $missing[] = 'Drivetrain (cartAttributes is missing)';
        }

        // 9. Inventory Status — derived from isUsed + isRental flags;
        //    these are booleans so they always exist, but isUsed must be explicitly set
        if (!array_key_exists('isUsed', $cart_data)) {
            $missing[] = 'Inventory Status (isUsed flag is missing)';
        }

        // 10. Brands — derived from make (same source as Manufacturers)
        //     Already covered by the make check above; only add if make is empty
        //     and not yet flagged
        if (empty(trim($make)) && !in_array('Categories (cartType.make is empty)', $missing)) {
            $missing[] = 'Brands (cartType.make is empty)';
        }

        $status = empty($missing) ? 'publish' : 'draft';

        if (!empty($missing)) {
            $cart_id = $cart_data['_id'] ?? 'unknown';
            error_log('[DMS Product Readiness] Cart ' . $cart_id . ' set to DRAFT — missing: ' . implode(', ', $missing));
        }

        return [
            'status'  => $status,
            'missing' => $missing,
        ];
    }

    /**
     * Check if the cart has enough data to produce a SKU.
     */
    private static function has_sku(array $cart_data): bool
    {
        // VIN or serial number present
        if (!empty($cart_data['vinNo']) || !empty($cart_data['serialNo'])) {
            return true;
        }

        // Fallback: generated from make + model + color + seatColor + location
        $make  = trim($cart_data['cartType']['make'] ?? '');
        $model = trim($cart_data['cartType']['model'] ?? '');
        $color = trim($cart_data['cartAttributes']['cartColor'] ?? '');

        // Need at least make + model + color for a meaningful generated SKU
        return !empty($make) && !empty($model) && !empty($color);
    }

    /**
     * Check if the location ID resolves to a known store.
     */
    private static function has_location(string $location_id): bool
    {
        if (empty($location_id)) {
            return false;
        }

        // Check the hardcoded Attributes::$locations map
        if (isset(Attributes::$locations[$location_id])) {
            $loc = Attributes::$locations[$location_id];
            // T0 is "National" — not a real store location
            return !empty($loc['city']) && !empty($loc['state']);
        }

        // Fallback: try the DMS API lookup (for dynamically added stores)
        if (class_exists('\Tigon\DmsConnect\Includes\DMS_API') || class_exists('DMS_API')) {
            $api_class = class_exists('\Tigon\DmsConnect\Includes\DMS_API')
                ? '\Tigon\DmsConnect\Includes\DMS_API'
                : 'DMS_API';
            if (method_exists($api_class, 'get_city_and_state_by_store_id')) {
                $store_data = $api_class::get_city_and_state_by_store_id($location_id);
                return !empty($store_data['city']) && !empty($store_data['state']);
            }
        }

        return false;
    }
}
