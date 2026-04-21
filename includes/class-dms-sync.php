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
     * Target dimensions for the primary (featured) product image.
     * All featured images are resized + center-cropped to these pixels so
     * every product card renders at the same size.
     */
    const PRIMARY_IMAGE_WIDTH  = 715;
    const PRIMARY_IMAGE_HEIGHT = 953;

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
            'total'     => 0,
        );

        $page_number = 0;
        $page_size = 20;

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

        return array(
            'success' => true,
            'stats'   => $stats,
        );
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
    private static function sync_product_images($product_id, $cart_data)
    {
        $image_urls = $cart_data['imageUrls'] ?? array();

        if (empty($image_urls) || !is_array($image_urls)) {
            return;
        }

        $attachment_ids = array();
        $featured_image_id = null;

        foreach ($image_urls as $index => $image_filename) {
            if (empty($image_filename)) {
                continue;
            }

            // Build full image URL
            $image_url = self::get_image_base_url() . ltrim($image_filename, '/');

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

        // Set featured image, using a separate cropped copy so the original
        // attachment is preserved untouched in the media library.
        if ($featured_image_id) {
            $cropped_id = self::ensure_cropped_copy($featured_image_id);
            set_post_thumbnail($product_id, $cropped_id ?: $featured_image_id);
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

        // Download image to temp file
        $temp_file = download_url($image_url);

        if (is_wp_error($temp_file)) {
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
     * Ensure a cropped copy exists for the given attachment and return its ID.
     *
     * The original attachment file is left untouched. When the original already
     * matches the target dimensions, the original ID is returned and no copy
     * is created. When a cropped copy has previously been generated for this
     * source at the target size, it is reused.
     *
     * @param int      $attachment_id Source attachment post ID.
     * @param int|null $width         Override target width.
     * @param int|null $height        Override target height.
     * @return int|false Attachment ID of the cropped copy (or original if already sized), or false on failure.
     */
    public static function ensure_cropped_copy($attachment_id, $width = null, $height = null)
    {
        $attachment_id = (int) $attachment_id;
        if ($attachment_id <= 0) {
            return false;
        }

        $width  = $width  ? (int) $width  : self::PRIMARY_IMAGE_WIDTH;
        $height = $height ? (int) $height : self::PRIMARY_IMAGE_HEIGHT;
        $size_key = '_dms_cropped_version_' . $width . 'x' . $height;

        // If this attachment is already a cropped copy at the target size, use it as-is.
        $source_of_crop = (int) get_post_meta($attachment_id, '_dms_cropped_from', true);
        if ($source_of_crop) {
            $self_meta = wp_get_attachment_metadata($attachment_id);
            if (!empty($self_meta['width']) && !empty($self_meta['height'])
                && (int) $self_meta['width'] === $width
                && (int) $self_meta['height'] === $height) {
                return $attachment_id;
            }
        }

        // Reuse a previously-generated crop for this source if it still exists.
        $existing_crop = (int) get_post_meta($attachment_id, $size_key, true);
        if ($existing_crop > 0 && get_post($existing_crop)) {
            return $existing_crop;
        }

        // If the original already matches the target size, no copy needed.
        $meta = wp_get_attachment_metadata($attachment_id);
        if (!empty($meta['width']) && !empty($meta['height'])
            && (int) $meta['width'] === $width
            && (int) $meta['height'] === $height) {
            return $attachment_id;
        }

        require_once ABSPATH . 'wp-admin/includes/image.php';

        $original_path = get_attached_file($attachment_id);
        if (!$original_path || !file_exists($original_path)) {
            return false;
        }

        $pathinfo  = pathinfo($original_path);
        $ext       = isset($pathinfo['extension']) ? $pathinfo['extension'] : 'jpg';
        $basename  = $pathinfo['filename'] . '-' . $width . 'x' . $height . '.' . $ext;
        $directory = $pathinfo['dirname'];
        $basename  = wp_unique_filename($directory, $basename);
        $new_path  = trailingslashit($directory) . $basename;

        $editor = wp_get_image_editor($original_path);
        if (is_wp_error($editor)) {
            return false;
        }

        $resized = $editor->resize($width, $height, true);
        if (is_wp_error($resized)) {
            return false;
        }

        $saved = $editor->save($new_path);
        if (is_wp_error($saved) || empty($saved['path']) || !file_exists($saved['path'])) {
            return false;
        }

        $saved_path = $saved['path'];
        $filetype   = wp_check_filetype($saved_path);
        $parent_id  = (int) wp_get_post_parent_id($attachment_id);

        $attachment_data = array(
            'post_mime_type' => $filetype['type'],
            'post_title'     => sanitize_file_name(pathinfo($saved_path, PATHINFO_FILENAME)),
            'post_content'   => '',
            'post_status'    => 'inherit',
        );

        $new_id = wp_insert_attachment($attachment_data, $saved_path, $parent_id);
        if (is_wp_error($new_id) || !$new_id) {
            @unlink($saved_path);
            return false;
        }

        $new_meta = wp_generate_attachment_metadata($new_id, $saved_path);
        if (!is_wp_error($new_meta) && !empty($new_meta)) {
            wp_update_attachment_metadata($new_id, $new_meta);
        }

        update_post_meta($new_id, '_dms_cropped_from', $attachment_id);
        update_post_meta($new_id, '_dms_primary_cropped', $width . 'x' . $height);
        update_post_meta($attachment_id, $size_key, $new_id);

        // Propagate DMS source URL so dedup logic can still trace origin.
        $source_url = get_post_meta($attachment_id, '_dms_image_url', true);
        if ($source_url) {
            update_post_meta($new_id, '_dms_image_url_source', $source_url);
        }

        return $new_id;
    }

    /**
     * Count products that have a featured image (for bulk progress reporting).
     *
     * @return int
     */
    public static function count_products_with_featured_image()
    {
        $query = new WP_Query(array(
            'post_type'      => 'product',
            'post_status'    => 'any',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'no_found_rows'  => false,
            'meta_query'     => array(
                array(
                    'key'     => '_thumbnail_id',
                    'compare' => 'EXISTS',
                ),
            ),
        ));

        return (int) $query->found_posts;
    }

    /**
     * Recrop featured images for a batch of existing products.
     *
     * @param int $offset     Offset into the product set.
     * @param int $batch_size Number of products to process per call.
     * @return array Batch stats + completion info.
     */
    public static function recrop_featured_images_batch($offset = 0, $batch_size = 10)
    {
        $offset     = max(0, (int) $offset);
        $batch_size = max(1, (int) $batch_size);

        $query = new WP_Query(array(
            'post_type'      => 'product',
            'post_status'    => 'any',
            'posts_per_page' => $batch_size,
            'offset'         => $offset,
            'orderby'        => 'ID',
            'order'          => 'ASC',
            'fields'         => 'ids',
            'no_found_rows'  => false,
            'meta_query'     => array(
                array(
                    'key'     => '_thumbnail_id',
                    'compare' => 'EXISTS',
                ),
            ),
        ));

        $product_ids = $query->posts;
        $total       = (int) $query->found_posts;

        $processed = 0;
        $skipped   = 0;
        $errors    = 0;

        foreach ($product_ids as $product_id) {
            $thumbnail_id = (int) get_post_thumbnail_id($product_id);
            if (!$thumbnail_id) {
                $skipped++;
                continue;
            }

            $cropped_id = self::ensure_cropped_copy($thumbnail_id);
            if (!$cropped_id) {
                $errors++;
                continue;
            }

            if ($cropped_id === $thumbnail_id) {
                // Already at target size or thumbnail already is the cropped copy.
                $skipped++;
                continue;
            }

            set_post_thumbnail($product_id, $cropped_id);
            $processed++;
        }

        $next_offset = $offset + count($product_ids);

        return array(
            'processed'   => $processed,
            'skipped'     => $skipped,
            'errors'      => $errors,
            'batch_count' => count($product_ids),
            'total'       => $total,
            'offset'      => $next_offset,
            'done'        => $next_offset >= $total || empty($product_ids),
            'width'       => self::PRIMARY_IMAGE_WIDTH,
            'height'      => self::PRIMARY_IMAGE_HEIGHT,
        );
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

