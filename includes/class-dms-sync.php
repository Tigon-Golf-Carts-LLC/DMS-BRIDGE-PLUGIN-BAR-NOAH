<?php
/**
 * DMS Inventory Sync Handler
 *
 * Background sync system for DMS inventory to WooCommerce products
 *
 * @package DMS_Bridge
 */

if (!defined('ABSPATH')) {
    exit;
}

class DMS_Sync
{
    /**
     * Image base URL for DMS images
     * Now uses centralized URL from DMS_API class
     *
     * @var string
     */
    private static function get_image_base_url()
    {
        return DMS_API::get_s3_carts_url();
    }

    /**
     * Sync all inventory from DMS API
     *
     * Fetches all carts using pagination and creates/updates WooCommerce products
     *
     * @return array Sync results with stats
     */
    public static function sync_inventory()
    {
        // Extend execution time for large inventories (30 minutes)
        @set_time_limit(1800);
        ignore_user_abort(true);

        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            return array(
                'success' => false,
                'message' => 'WooCommerce is not active',
            );
        }

        $stats = array(
            'created'   => 0,
            'updated'   => 0,
            'skipped'   => 0,
            'errors'    => 0,
            'sold'      => 0,
            'total'     => 0,
        );

        // Collect all active DMS cart IDs so we can detect sold products afterwards
        $active_cart_ids = array();

        // Deduplication set for new carts (mirrors import.js + Core.php)
        // Key: make|model|cartColor|seatColor|locationId
        $seen_new = array();

        $page_number = 0;
        $page_size = 50;

        // Paginate through all carts
        // The /get-carts API returns a raw JSON array: [{...}, {...}, {...}]
        // Pagination stops when an empty array is returned
        while (true) {
            // Fetch carts for this page
            $carts = DMS_API::get_carts($page_number, $page_size);

            // Check for API error
            if ($carts === false) {
                $stats['errors']++;
                break; // Stop on error
            }

            // Guard: ensure we have an array
            if (!is_array($carts)) {
                $stats['errors']++;
                break;
            }

            // Stop pagination when API returns an empty array
            if (empty($carts)) {
                break;
            }

            // Process each cart
            foreach ($carts as $cart_data) {
                $stats['total']++;

                // Skip if cart doesn't have _id
                if (empty($cart_data['_id'])) {
                    $stats['skipped']++;
                    continue;
                }

                $cart_id = $cart_data['_id'];

                // ---------------------------------------------------------
                // Cart eligibility filters (mirrors import.js + Core.php)
                //
                // The DMS API may return ALL carts without pre-filtering.
                // Apply the same three-state condition from Abstract_Cart:
                //   if (isInStock && !isInBoneyard && needOnWebsite) → create/update
                //   else → delete/skip
                // ---------------------------------------------------------

                // Check eligibility using centralized logic
                if (!\Tigon\DmsConnect\Includes\Product_Manager::should_be_on_website($cart_data)) {
                    // Cart is ineligible — delete existing product if one exists
                    $existing_pid = function_exists('tigon_dms_get_product_by_cart_id')
                        ? tigon_dms_get_product_by_cart_id($cart_id)
                        : false;
                    if ($existing_pid) {
                        \Tigon\DmsConnect\Includes\Product_Media::delete_product((int) $existing_pid);
                        $stats['sold']++;
                    } else {
                        $stats['skipped']++;
                    }
                    continue;
                }

                // Skip carts with DELETE in serialNo or vinNo (mirrors Core.php:506-512)
                $serial = strtoupper($cart_data['serialNo'] ?? '');
                $vin    = strtoupper($cart_data['vinNo'] ?? '');
                if (str_contains($serial, 'DELETE') || str_contains($vin, 'DELETE')) {
                    $stats['skipped']++;
                    continue;
                }

                // Deduplicate new carts by unique identifiers (_id, vinNo, serialNo)
                // instead of make/model/color/seat/location — multiple carts of the
                // same model/color are distinct inventory items with unique IDs.
                $is_used = !empty($cart_data['isUsed']);
                if (!$is_used) {
                    $dominated = false;

                    // Primary dedup: DMS cart _id (unique per cart)
                    if (!empty($cart_id)) {
                        if (isset($seen_new['id:' . $cart_id])) {
                            $dominated = true;
                        }
                        $seen_new['id:' . $cart_id] = true;
                    }

                    // Secondary dedup: vinNo (matches CMS SKU priority 1)
                    $vin_val = trim($cart_data['vinNo'] ?? '');
                    if (!$dominated && $vin_val !== '') {
                        if (isset($seen_new['vin:' . $vin_val])) {
                            $dominated = true;
                        }
                        $seen_new['vin:' . $vin_val] = true;
                    }

                    // Tertiary dedup: serialNo (matches CMS SKU priority 2)
                    $serial_val = trim($cart_data['serialNo'] ?? '');
                    if (!$dominated && $serial_val !== '') {
                        if (isset($seen_new['sn:' . $serial_val])) {
                            $dominated = true;
                        }
                        $seen_new['sn:' . $serial_val] = true;
                    }

                    if ($dominated) {
                        $stats['skipped']++;
                        continue;
                    }
                }

                // Cart passed all filters — track as active
                $active_cart_ids[] = $cart_id;

                // Stage cart data in local tigon_dms_carts table
                // This preserves ALL DMS fields locally for debugging/querying
                $store_id   = $cart_data['cartLocation']['locationId'] ?? '';
                $store_name = DMS_API::get_city_by_store_id($store_id);
                \Tigon\DmsConnect\Admin\CartModel::upsert_from_api($cart_data, $store_name, '', $store_id);

                // Check if product already exists (for stats tracking)
                $existing_product_id = tigon_dms_get_product_by_cart_id($cart_id);
                $was_existing = (bool) $existing_product_id;

                try {
                    // Use existing function to create/update product (idempotent)
                    $product_id = tigon_dms_ensure_woo_product($cart_data, $cart_id);

                    if ($product_id) {
                        // Handle images (featured + gallery)
                        self::sync_product_images($product_id, $cart_data);

                        // Track stats
                        if ($was_existing) {
                            $stats['updated']++;
                        } else {
                            $stats['created']++;
                        }

                        // Report PID and URL back to DMS (mirrors Core.php:478-488)
                        self::report_pid_to_dms($cart_id, $product_id);
                    } else {
                        $stats['errors']++;
                    }
                } catch (Exception $e) {
                    $stats['errors']++;
                    error_log('DMS Sync Error for cart ' . $cart_id . ': ' . $e->getMessage());
                }
            }

            // Move to next page
            $page_number++;

            // Prevent infinite loops (safety limit)
            if ($page_number > 1000) {
                break;
            }
        }

        // -----------------------------------------------------------------
        // Sold product detection
        //
        // Find WooCommerce products with _dms_cart_id that are no longer
        // in the active DMS inventory and fully delete them (product +
        // all media attachments).
        // -----------------------------------------------------------------
        if (!empty($active_cart_ids)) {
            $sold_products = self::detect_sold_products($active_cart_ids);
            foreach ($sold_products as $sold_product_id) {
                $deleted = \Tigon\DmsConnect\Includes\Product_Media::delete_product((int) $sold_product_id);
                if ($deleted) {
                    $stats['sold']++;
                }
            }
        }

        // -----------------------------------------------------------------
        // Post-import: global WC lookup table refresh
        // (mirrors Abstract_Import_Controller::process_post_import and
        //  Core.php:557 — called once after ALL products are processed)
        // -----------------------------------------------------------------
        if (function_exists('wc_update_product_lookup_tables')) {
            wc_update_product_lookup_tables();
        }

        // Clear DMS API transient caches so the next frontend page load
        // picks up the freshly-synced data instead of stale cached results.
        DMS_API::clear_caches();

        return array(
            'success' => true,
            'stats'   => $stats,
        );
    }


    /**
     * Detect WooCommerce products whose DMS cart IDs are no longer active.
     *
     * Queries all published and draft products with a _dms_cart_id meta and
     * returns the product IDs whose cart ID is NOT in the active set.
     * Draft products are included because they may be waiting for complete
     * mappings but are still active DMS inventory.
     *
     * @param array $active_cart_ids Array of DMS cart IDs still in inventory
     * @return array Product IDs that are no longer in DMS
     */
    private static function detect_sold_products($active_cart_ids)
    {
        global $wpdb;

        // Get all published and draft products that have a DMS cart ID
        $results = $wpdb->get_results(
            "SELECT p.ID, pm.meta_value AS cart_id
             FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_dms_cart_id'
             WHERE p.post_type = 'product' AND p.post_status IN ('publish', 'draft')",
            ARRAY_A
        );

        if (empty($results)) {
            return array();
        }

        $sold_ids = array();
        $active_set = array_flip($active_cart_ids); // O(1) lookups

        foreach ($results as $row) {
            if (!isset($active_set[$row['cart_id']])) {
                $sold_ids[] = (int) $row['ID'];
            }
        }

        return $sold_ids;
    }

    /**
     * Report product ID and URL back to DMS after successful create/update.
     *
     * Mirrors Core.php:478-488 — tells DMS that this cart is now on the
     * website with its permalink, so the DMS dashboard shows accurate status.
     *
     * @param string $cart_id    DMS cart _id
     * @param int    $product_id WooCommerce product ID
     */
    private static function report_pid_to_dms($cart_id, $product_id)
    {
        if (empty($cart_id) || empty($product_id)) {
            return;
        }

        // Use DMS_Connector if available (class-based API path)
        if (class_exists('\Tigon\DmsConnect\Includes\DMS_Connector')) {
            try {
                $pid_request = wp_json_encode(array(array(
                    '_id' => $cart_id,
                    'pid' => $product_id,
                    'advertising' => array(
                        'onWebsite'  => true,
                        'websiteUrl' => get_permalink($product_id),
                    ),
                )));
                \Tigon\DmsConnect\Includes\DMS_Connector::request($pid_request, '/chimera/carts', 'PUT');
            } catch (\Exception $e) {
                error_log('DMS Sync: Failed to report PID ' . $product_id . ' for cart ' . $cart_id . ': ' . $e->getMessage());
            }
            return;
        }

        // Fallback: use DMS_API if it has an update method
        if (method_exists('DMS_API', 'update_cart')) {
            DMS_API::update_cart($cart_id, array(
                'pid' => $product_id,
                'advertising' => array(
                    'onWebsite'  => true,
                    'websiteUrl' => get_permalink($product_id),
                ),
            ));
        }
    }

    /**
     * Sync product images from DMS cart data
     *
     * Sets first image as featured image, rest as gallery images
     * Avoids duplicate uploads by checking existing attachment URLs
     *
     * @param int   $product_id WooCommerce product ID
     * @param array $cart_data  Full DMS cart payload
     * @return void
     */
    /**
     * Public wrapper for sync_product_images (used by selective sync AJAX handler).
     */
    public static function sync_product_images_public($product_id, $cart_data)
    {
        self::sync_product_images($product_id, $cart_data);
    }

    private static function sync_product_images($product_id, $cart_data)
    {
        // WooCommerce placeholder image attachment ID (used when DMS has no images)
        $placeholder_image_id = 204304;

        // Check if the DMS payload has real images (not resolved/fallback URLs)
        $raw_image_urls = $cart_data['imageUrls'] ?? array();
        $has_real_images = !empty($raw_image_urls) && is_array($raw_image_urls);

        if (!$has_real_images) {
            // No real images from DMS — use WooCommerce placeholder
            $current_thumb = get_post_thumbnail_id($product_id);
            if (!$current_thumb || (int) $current_thumb === $placeholder_image_id) {
                set_post_thumbnail($product_id, $placeholder_image_id);
                update_post_meta($product_id, '_product_image_gallery', '');
            }
            return;
        }

        // Real images arrived — remove placeholder if it was the featured image
        $current_thumb = get_post_thumbnail_id($product_id);
        if ((int) $current_thumb === $placeholder_image_id) {
            delete_post_thumbnail($product_id);
        }

        // Use centralized image resolver (handles coming-soon placeholder)
        $resolved_urls = DMS_API::resolve_cart_image_urls($cart_data);

        if (empty($resolved_urls)) {
            return;
        }

        $attachment_ids = array();
        $featured_image_id = null;

        foreach ($resolved_urls as $index => $image_url) {
            if (empty($image_url)) {
                continue;
            }

            // Check if attachment already exists by URL
            $existing_attachment_id = self::get_attachment_id_by_url($image_url);

            if ($existing_attachment_id) {
                // Use existing attachment
                $attachment_id = $existing_attachment_id;
            } else {
                // Upload new image
                $attachment_id = self::upload_image_from_url($image_url, $product_id);

                if (!$attachment_id) {
                    continue; // Skip on upload failure
                }
            }

            if ($attachment_id) {
                $attachment_ids[] = $attachment_id;

                // First image is featured image
                if ($index === 0) {
                    $featured_image_id = $attachment_id;
                }
            }
        }

        // Set featured image
        if ($featured_image_id) {
            set_post_thumbnail($product_id, $featured_image_id);
        }

        // Set gallery images (all images except featured)
        $gallery_ids = array_slice($attachment_ids, 1); // Skip first image
        if (!empty($gallery_ids)) {
            update_post_meta($product_id, '_product_image_gallery', implode(',', $gallery_ids));
        } else {
            // No gallery images, clear the meta
            update_post_meta($product_id, '_product_image_gallery', '');
        }
    }

    /**
     * Get attachment ID by URL (check if image already exists)
     *
     * @param string $image_url Full image URL
     * @return int|false Attachment ID or false if not found
     */
    private static function get_attachment_id_by_url($image_url)
    {
        global $wpdb;

        // Extract filename from URL
        $filename = basename(parse_url($image_url, PHP_URL_PATH));

        if (empty($filename)) {
            return false;
        }

        // Search for attachment by filename in postmeta
        $attachment_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT post_id FROM {$wpdb->postmeta}
                 WHERE meta_key = '_wp_attached_file'
                 AND meta_value LIKE %s
                 LIMIT 1",
                '%' . $wpdb->esc_like($filename) . '%'
            )
        );

        if ($attachment_id) {
            return (int) $attachment_id;
        }

        // Also check by URL in postmeta (for externally hosted images)
        $attachment_id = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT post_id FROM {$wpdb->postmeta}
                 WHERE meta_key = '_dms_image_url'
                 AND meta_value = %s
                 LIMIT 1",
                $image_url
            )
        );

        return $attachment_id ? (int) $attachment_id : false;
    }

    /**
     * Upload image from URL and attach to product
     *
     * @param string $image_url   Full image URL
     * @param int    $product_id  WooCommerce product ID
     * @return int|false Attachment ID or false on failure
     */
    private static function upload_image_from_url($image_url, $product_id)
    {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        // Download image to temp file (30 second timeout per image)
        $temp_file = download_url($image_url, 30);

        if (is_wp_error($temp_file)) {
            error_log('[DMS Sync] Image download failed for ' . $image_url . ': ' . $temp_file->get_error_message());
            return false;
        }

        // Prepare file array for wp_handle_sideload
        $file_array = array(
            'name'     => basename(parse_url($image_url, PHP_URL_PATH)),
            'tmp_name' => $temp_file,
        );

        // Handle sideload
        $file = wp_handle_sideload($file_array, array('test_form' => false));

        if (isset($file['error'])) {
            @unlink($temp_file);
            return false;
        }

        // Create attachment
        $attachment_data = array(
            'post_mime_type' => $file['type'],
            'post_title'     => sanitize_file_name(pathinfo($file_array['name'], PATHINFO_FILENAME)),
            'post_content'   => '',
            'post_status'    => 'inherit',
        );

        $attachment_id = wp_insert_attachment($attachment_data, $file['file'], $product_id);

        if (is_wp_error($attachment_id)) {
            @unlink($file['file']);
            return false;
        }

        // Generate attachment metadata
        $attach_data = wp_generate_attachment_metadata($attachment_id, $file['file']);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        // Store original URL in meta to avoid duplicates
        update_post_meta($attachment_id, '_dms_image_url', $image_url);

        return $attachment_id;
    }

    /**
     * Get sync interval option (in hours)
     *
     * @return int Hours between syncs
     */
    public static function get_sync_interval()
    {
        return get_option('dms_sync_interval', 6); // Default: 6 hours
    }

    /**
     * Set sync interval option (in hours)
     *
     * @param int $hours Hours between syncs
     * @return void
     */
    public static function set_sync_interval($hours)
    {
        update_option('dms_sync_interval', max(1, (int) $hours));
    }
}

