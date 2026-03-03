<?php
/**
 * DMS API Handler
 *
 * @package DMS_Bridge
 */

if (!defined('ABSPATH')) {
    exit;
}

class DMS_API
{
    /**
     * API endpoint URLs
     *
     * @var string
     */
    private static $api_url = 'https://api.tigondms.com/wp-website/get-featured-carts';
    private static $stores_url = 'https://api.tigondms.com/wp-website/tigon-stores';
    private static $cart_by_id_url = 'https://api.tigondms.com/wp-website/get-cart-by-id';
    private static $get_carts_url = 'https://api.tigondms.com/wp-website/get-carts';
    
    /**
     * S3 Bucket URLs
     *
     * @var string
     */
    private static $s3_carts_url = 'https://s3.amazonaws.com/test.docs.s3/carts/';
    private static $s3_window_stickers_url = 'https://s3.us-east-1.amazonaws.com/prod.docs.s3/cart-window-stickers/';
    
    /**
     * Cached stores data for current page load (static variable to avoid multiple API calls)
     *
     * @var array|null
     */
    private static $cached_stores_data = null;

    /**
     * Fetch featured carts from DMS API
     *
     * @param string $key Location key (e.g., 'national', 'tigon_hatfield')
     * @return array
     */
    public static function get_featured_carts($key = 'national')
    {
        // Create unique transient key for this location
        $transient_key = 'dms_carts_' . sanitize_key($key);

        // Try to get cached data
        // $cached_data = get_transient($transient_key);
        // if ($cached_data !== false) {
        //     return $cached_data;
        // }

        // No cache, fetch from API
        $response = wp_remote_post(
            self::$api_url,
            array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'body'    => wp_json_encode(
                    array(
                        'key' => sanitize_text_field($key),
                    )
                ),
                'timeout' => 20,
            )
        );

        if (is_wp_error($response)) {
            return array();
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!is_array($data)) {
            return array();
        }

        // Cache for 5 minutes (300 seconds) - COMMENTED OUT
        // set_transient($transient_key, $data, 300);

        return $data;
    }

    /**
     * Fetch store locations with 1-hour cache
     * Uses static variable to cache data for current page load (avoid multiple API calls)
     *
     * @return array
     */
    public static function get_stores()
    {
        // Return cached data if already loaded in this page request
        if (self::$cached_stores_data !== null) {
            return self::$cached_stores_data;
        }

        $transient_key = 'dms_stores';

        // Try to get cached data from WordPress transients
        $cached_data = get_transient($transient_key);
        if ($cached_data !== false) {
            self::$cached_stores_data = $cached_data;
            return $cached_data;
        }

        // No cache, fetch from API
        $response = wp_remote_get(
            self::$stores_url,
            array(
                'timeout' => 15,
            )
        );

        if (is_wp_error($response)) {
            self::$cached_stores_data = array();
            return array();
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!is_array($data)) {
            self::$cached_stores_data = array();
            return array();
        }

        // Cache for 1 hour (3600 seconds) since stores rarely change
        set_transient($transient_key, $data, 3600);
        
        // Store in static variable for this page load
        self::$cached_stores_data = $data;

        return $data;
    }

    /**
     * Get city name by store ID
     *
     * @param string $store_id Store ID (e.g., 'T1', 'T2')
     * @return string City name or store ID if not found
     */
    public static function get_city_by_store_id($store_id)
    {
        $stores = self::get_stores();

        foreach ($stores as $store) {
            if (isset($store['storeId']) && $store['storeId'] === $store_id) {
                return $store['address']['city'] ?? $store_id;
            }
        }

        return $store_id; // Fallback to store ID if not found
    }

    /**
     * Get city and state by store ID
     * Uses cached stores data from get_stores() to avoid multiple API calls
     *
     * @param string $store_id Store ID (e.g., 'T1', 'T2')
     * @return array Array with 'city' and 'state' keys, or array with store_id if not found
     */
    public static function get_city_and_state_by_store_id($store_id)
    {
        $stores = self::get_stores();

        foreach ($stores as $store) {
            if (isset($store['storeId']) && $store['storeId'] === $store_id) {
                return array(
                    'city' => $store['address']['city'] ?? '',
                    'state' => $store['address']['state'] ?? '',
                );
            }
        }

        // Fallback to store ID if not found
        return array(
            'city' => $store_id,
            'state' => '',
        );
    }

    /**
     * Fetch a single cart by cartId
     *
     * @param string $cart_id The cart ID (_id from DMS)
     * @return array|false Cart data or false on error
     */
    public static function get_cart($cart_id)
    {
        if (empty($cart_id)) {
            return false;
        }

        // Create transient key for this cart
        $transient_key = 'dms_cart_' . sanitize_key($cart_id);

        // Try to get cached data (5 minutes cache)
        $cached_data = get_transient($transient_key);
        if ($cached_data !== false) {
            return $cached_data;
        }

        // No cache, fetch from API
        $response = wp_remote_post(
            self::$cart_by_id_url,
            array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'body'    => wp_json_encode(
                    array(
                        'cartId' => sanitize_text_field($cart_id),
                    )
                ),
                'timeout' => 20,
            )
        );

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        if (!is_array($data) || empty($data)) {
            return false;
        }

        // Cache for 5 minutes (300 seconds)
        set_transient($transient_key, $data, 300);

        return $data;
    }

    /**
     * Fetch carts from DMS API with pagination (for background sync)
     *
     * The /get-carts endpoint returns a raw JSON array of carts: [{...}, {...}, {...}]
     *
     * @param int $page_number Page number (starting at 0)
     * @param int $page_size   Number of carts per page (default: 20)
     * @return array|false Array of carts, or false on error
     */
    public static function get_carts($page_number = 0, $page_size = 20)
    {
        $response = wp_remote_post(
            self::$get_carts_url,
            array(
                'headers' => array(
                    'Content-Type' => 'application/json',
                ),
                'body'    => wp_json_encode(
                    array(
                        'pageNumber' => max(0, (int) $page_number),
                        'pageSize'   => max(1, min(100, (int) $page_size)), // Clamp between 1-100
                    )
                ),
                'timeout' => 30, // Longer timeout for sync operations
            )
        );

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);
        
        // Guard: ensure we have an array (raw JSON array from API)
        $carts = is_array($decoded) ? $decoded : array();
        
        return $carts;
    }

    /**
     * Get S3 carts bucket URL
     *
     * @return string S3 carts bucket URL
     */
    public static function get_s3_carts_url()
    {
        return self::$s3_carts_url;
    }

    /**
     * Get S3 window stickers bucket URL
     *
     * @return string S3 window stickers bucket URL
     */
    public static function get_s3_window_stickers_url()
    {
        return self::$s3_window_stickers_url;
    }

}
