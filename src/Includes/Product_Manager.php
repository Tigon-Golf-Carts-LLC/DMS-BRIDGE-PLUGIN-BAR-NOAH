<?php
/**
 * Product Manager
 *
 * Central entry point for product lifecycle operations. Delegates to the
 * existing functions in dms-bridge-plugin.php while providing a clean
 * class-based API.
 *
 * This class handles:
 * - Product creation, update, and deletion
 * - isInStock / sold-product enforcement
 * - Finding existing products by DMS cart ID
 *
 * @package Tigon\DmsConnect\Includes
 */

namespace Tigon\DmsConnect\Includes;

if (!defined('ABSPATH')) {
    exit;
}

class Product_Manager
{
    /**
     * Create or update a WooCommerce product from DMS cart data.
     *
     * Delegates to tigon_dms_ensure_woo_product() which handles the full
     * product creation pipeline including categories, images, SEO, etc.
     *
     * @param array  $cart_data Full DMS cart payload.
     * @param string $cart_id   The DMS cart _id.
     * @return int|false Product ID on success, false on failure.
     */
    public static function ensure_product(array $cart_data, string $cart_id)
    {
        if (!function_exists('tigon_dms_ensure_woo_product')) {
            return false;
        }
        return tigon_dms_ensure_woo_product($cart_data, $cart_id);
    }

    /**
     * Find a WooCommerce product by its DMS cart ID.
     *
     * @param string $cart_id DMS cart _id.
     * @return int|false Product ID if found, false otherwise.
     */
    public static function find_by_cart_id(string $cart_id)
    {
        if (function_exists('tigon_dms_get_product_by_cart_id')) {
            return tigon_dms_get_product_by_cart_id($cart_id);
        }

        global $wpdb;
        return $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta}
             WHERE meta_key = '_dms_cart_id' AND meta_value = %s
             LIMIT 1",
            $cart_id
        )) ?: false;
    }

    /**
     * Handle a sold/out-of-stock product.
     *
     * If isInStock is false, null, or empty, the product is fully deleted
     * from WordPress, WooCommerce, and the database — including all media
     * attachments (images, gallery, monroney, etc.).
     *
     * @param int $product_id WooCommerce product ID.
     * @return bool True if deleted, false if not found.
     */
    public static function handle_sold_product(int $product_id): bool
    {
        return Product_Media::delete_product($product_id);
    }

    /**
     * Check if a DMS cart should be on the website.
     *
     * A cart belongs on the website only if ALL conditions are met:
     * - isInStock is truthy (not empty, null, or false)
     * - isInBoneyard is falsy
     * - needOnWebsite is truthy (from advertising.needOnWebsite)
     *
     * @param array $cart_data DMS cart payload.
     * @return bool True if the cart should be published as a product.
     */
    public static function should_be_on_website(array $cart_data): bool
    {
        $is_in_stock = !empty($cart_data['isInStock']);
        $is_in_boneyard = !empty($cart_data['isInBoneyard']);
        $need_on_website = !empty($cart_data['advertising']['needOnWebsite']);

        return $is_in_stock && !$is_in_boneyard && $need_on_website;
    }

    /**
     * Get the specific reason(s) why a cart is not eligible for the website.
     *
     * Returns a human-readable string listing every failed condition.
     * If the cart IS eligible, returns an empty string.
     *
     * @param array $cart_data DMS cart payload.
     * @return string Reason string, e.g. "isInStock=false, isInBoneyard=true".
     */
    public static function get_ineligibility_reason(array $cart_data): string
    {
        $reasons = [];

        if (empty($cart_data['isInStock'])) {
            $reasons[] = 'isInStock=false';
        }
        if (!empty($cart_data['isInBoneyard'])) {
            $reasons[] = 'isInBoneyard=true';
        }
        if (empty($cart_data['advertising']['needOnWebsite'])) {
            $reasons[] = 'needOnWebsite=false';
        }

        return implode(', ', $reasons);
    }

    /**
     * Process a DMS cart: create/update if eligible, delete if not.
     *
     * This is the main entry point for sync operations. It checks
     * isInStock and other eligibility flags, then either ensures the
     * product exists or deletes it.
     *
     * @param array  $cart_data Full DMS cart payload.
     * @param string $cart_id   The DMS cart _id.
     * @return array{action: string, product_id: int|false} Result with action taken.
     */
    public static function sync_cart(array $cart_data, string $cart_id): array
    {
        if (self::should_be_on_website($cart_data)) {
            $product_id = self::ensure_product($cart_data, $cart_id);
            return [
                'action'     => $product_id ? 'synced' : 'error',
                'product_id' => $product_id,
            ];
        }

        // Cart should not be on website — find and delete existing product
        $existing_id = self::find_by_cart_id($cart_id);
        if ($existing_id) {
            self::handle_sold_product((int) $existing_id);
            return [
                'action'     => 'deleted',
                'product_id' => (int) $existing_id,
            ];
        }

        return [
            'action'     => 'skipped',
            'product_id' => false,
        ];
    }

    /**
     * Refresh WooCommerce lookup table and caches for a product.
     *
     * @param int $product_id
     */
    public static function refresh_product_data(int $product_id): void
    {
        if (function_exists('tigon_dms_refresh_wc_product_data')) {
            tigon_dms_refresh_wc_product_data($product_id);
            return;
        }

        // Inline fallback
        global $wpdb;
        $wpdb->delete($wpdb->prefix . 'wc_product_meta_lookup', ['product_id' => $product_id]);

        if (function_exists('wc_update_product_lookup_tables')) {
            wc_update_product_lookup_tables();
        }
        clean_post_cache($product_id);
        if (function_exists('wc_delete_product_transients')) {
            wc_delete_product_transients($product_id);
        }
    }
}
