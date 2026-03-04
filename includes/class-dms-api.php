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
    private static $s3_window_stickers_url = 'https://s3.amazonaws.com/prod.docs.s3/cart-window-stickers/';
    private static $s3_default_images_url = 'https://s3.amazonaws.com/test.docs.s3/default-cart-web-images/';

    /**
     * Placeholder image for carts without public images
     * (most carts only have internalCartImageUrls which are not publicly accessible)
     */
    private static $coming_soon_image = 'https://tigongolfcarts.com/wp-content/uploads/2024/11/TIGON-GOLF-CARTS-IMAGES-COMING-SOON.jpg';
    
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
        $transient_key = 'dms_carts_' . sanitize_key($key);

        // Return cached data — only refreshed when sync runs
        $cached_data = get_transient($transient_key);
        if ($cached_data !== false) {
            return $cached_data;
        }

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

        // Cache for 24 hours — cleared explicitly when sync runs
        set_transient($transient_key, $data, DAY_IN_SECONDS);

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

        if (!is_array($decoded)) {
            return array();
        }

        // API returns { carts: [...], totalCarts: N } — extract the carts array
        if (isset($decoded['carts']) && is_array($decoded['carts'])) {
            return $decoded['carts'];
        }

        // Fallback: if response is already a flat array of carts, return as-is
        return $decoded;
    }

    /**
     * Fetch one page of carts AND the total count.
     *
     * Same endpoint as get_carts() but returns the full envelope so callers
     * can paginate without fetching everything up front.
     *
     * @param int $page_number Page number (starting at 0)
     * @param int $page_size   Carts per page (clamped 1-100)
     * @return array{carts: array, total: int}|false  False on error.
     */
    public static function get_carts_page($page_number = 0, $page_size = 50)
    {
        $response = wp_remote_post(
            self::$get_carts_url,
            array(
                'headers' => array('Content-Type' => 'application/json'),
                'body'    => wp_json_encode(array(
                    'pageNumber' => max(0, (int) $page_number),
                    'pageSize'   => max(1, min(100, (int) $page_size)),
                )),
                'timeout' => 30,
            )
        );

        if (is_wp_error($response)) {
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $decoded = json_decode($body, true);

        if (!is_array($decoded)) {
            return false;
        }

        // Envelope format: { carts: [...], totalCarts: N }
        if (isset($decoded['carts']) && is_array($decoded['carts'])) {
            return array(
                'carts' => $decoded['carts'],
                'total' => isset($decoded['totalCarts']) ? (int) $decoded['totalCarts'] : count($decoded['carts']),
            );
        }

        // Flat array fallback (old API format)
        return array(
            'carts' => $decoded,
            'total' => count($decoded),
        );
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

    /**
     * Get the "Coming Soon" placeholder image URL
     *
     * @return string
     */
    public static function get_coming_soon_image()
    {
        return self::$coming_soon_image;
    }

    /**
     * Resolve public image URLs for a cart.
     *
     * Priority order:
     * 1. `imageUrls` from the API payload (prod S3 bucket)
     * 2. Default cart images for NEW carts (test.docs.s3 bucket) — location-specific first, national fallback
     * 3. Coming-soon placeholder
     *
     * Default image naming: {color}-{make}-{model}-in-{location}-{N}.jpg
     * New carts normally have 4 default images (index 1-4).
     * Used carts do NOT get default images.
     *
     * @param array $cart_data Full cart payload
     * @return array Array of full image URLs
     */
    public static function resolve_cart_image_urls(array $cart_data): array
    {
        $image_filenames = $cart_data['imageUrls'] ?? array();

        // 1. Use prod S3 images if available
        if (!empty($image_filenames) && is_array($image_filenames)) {
            $urls = array();
            foreach ($image_filenames as $filename) {
                if (!empty($filename)) {
                    $urls[] = self::$s3_carts_url . ltrim($filename, '/');
                }
            }
            if (!empty($urls)) {
                return $urls;
            }
        }

        // 2. For NEW carts only, try default cart images from test.docs.s3
        $is_used = !empty($cart_data['isUsed']);
        if (!$is_used) {
            $default_urls = self::resolve_default_cart_images($cart_data);
            if (!empty($default_urls)) {
                return $default_urls;
            }
        }

        // 3. Fallback to coming-soon placeholder
        return array(self::$coming_soon_image);
    }

    /**
     * Resolve default cart images from the test.docs.s3 bucket for NEW carts.
     *
     * Naming pattern: {color}-{make}-{model}-in-{location}-{N}.jpg
     * where {location} is "{city}-{state}" (e.g., "ocean-view-new-jersey")
     * or "national" for the national fallback.
     *
     * Tries location-specific images first, falls back to national.
     * Each new cart normally has 4 images (index 1-4).
     *
     * @param array $cart_data Full cart payload
     * @return array Array of image URLs, or empty array if none found
     */
    private static function resolve_default_cart_images(array $cart_data): array
    {
        $color = $cart_data['cartAttributes']['cartColor'] ?? '';
        $make  = $cart_data['cartType']['make'] ?? '';
        $model = $cart_data['cartType']['model'] ?? '';
        $store_id = $cart_data['cartLocation']['locationId'] ?? '';

        if (empty($color) || empty($make) || empty($model)) {
            return array();
        }

        // Build the base filename: sanitize to lowercase hyphenated
        $color_slug = sanitize_title($color);
        $make_slug  = sanitize_title(preg_replace('/[®™]/', '', $make));
        $model_slug = sanitize_title($model);

        // Resolve city and state for location-specific images
        $city = '';
        $state = '';
        if (!empty($store_id)) {
            if (class_exists('\Tigon\DmsConnect\Admin\Attributes')) {
                $loc = \Tigon\DmsConnect\Admin\Attributes::get_location($store_id);
                if ($loc) {
                    $city  = $loc['city'] ?? '';
                    $state = $loc['state'] ?? '';
                }
            }
            if (empty($city)) {
                $store_data = self::get_city_and_state_by_store_id($store_id);
                $city  = $store_data['city'] ?? '';
                $state = $store_data['state'] ?? '';
            }
        }

        $base_prefix = $color_slug . '-' . $make_slug . '-' . $model_slug . '-in-';
        $image_count = 4;

        // Try location-specific images first
        if (!empty($city) && !empty($state)) {
            $location_slug = sanitize_title($city . ' ' . $state);
            $location_urls = self::build_default_image_urls($base_prefix . $location_slug, $image_count);
            if (!empty($location_urls)) {
                return $location_urls;
            }
        }

        // Fall back to national images
        $national_urls = self::build_default_image_urls($base_prefix . 'national', $image_count);
        if (!empty($national_urls)) {
            return $national_urls;
        }

        return array();
    }

    /**
     * Build default image URLs and verify at least the first one exists.
     *
     * @param string $base_name  Base filename without index (e.g., "arctic-gray-evolution-classic-2-pro-in-national")
     * @param int    $count      Number of images to try (default 4)
     * @return array Array of verified image URLs, or empty if first image doesn't exist
     */
    private static function build_default_image_urls(string $base_name, int $count = 4): array
    {
        // Check if the first image exists via a HEAD request
        $first_url = self::$s3_default_images_url . $base_name . '-1.jpg';

        $response = wp_remote_head($first_url, array(
            'timeout'   => 5,
            'sslverify' => false,
        ));

        if (is_wp_error($response)) {
            return array();
        }

        $status_code = wp_remote_retrieve_response_code($response);
        if ($status_code !== 200) {
            return array();
        }

        // First image exists — build all URLs (1 through $count)
        $urls = array();
        for ($i = 1; $i <= $count; $i++) {
            $urls[] = self::$s3_default_images_url . $base_name . '-' . $i . '.jpg';
        }

        return $urls;
    }

    /**
     * Clear all DMS API transient caches.
     *
     * Call this after a sync completes (scheduled or manual) so the next
     * frontend page load fetches fresh data from the API.
     */
    public static function clear_caches()
    {
        global $wpdb;

        // Delete all dms_carts_* transients (featured carts per location)
        $wpdb->query(
            "DELETE FROM {$wpdb->options}
             WHERE option_name LIKE '_transient_dms_carts_%'
                OR option_name LIKE '_transient_timeout_dms_carts_%'"
        );

        // Delete individual cart transients
        $wpdb->query(
            "DELETE FROM {$wpdb->options}
             WHERE option_name LIKE '_transient_dms_cart_%'
                OR option_name LIKE '_transient_timeout_dms_cart_%'"
        );

        // Delete stores transient
        delete_transient('dms_stores');

        // Reset static cache
        self::$cached_stores_data = null;
    }
}
