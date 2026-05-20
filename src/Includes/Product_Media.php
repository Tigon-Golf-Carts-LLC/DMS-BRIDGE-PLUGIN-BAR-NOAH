<?php
/**
 * Product Media Helper
 *
 * Centralized logic for deleting product media attachments (images, gallery,
 * monroney stickers, etc.) and for fully removing a product with all its
 * associated media from WordPress/WooCommerce.
 *
 * @package Tigon\DmsConnect\Includes
 */

namespace Tigon\DmsConnect\Includes;

if (!defined('ABSPATH')) {
    exit;
}

class Product_Media
{
    /**
     * Delete all media attachments for a WooCommerce product.
     *
     * Removes:
     * - Featured image (wp_postmeta _thumbnail_id)
     * - Gallery images (wp_postmeta _product_image_gallery)
     * - Monroney sticker PDF attachment (wp_postmeta monroney_sticker)
     * - Any other attachments whose post_parent is this product
     *
     * Each attachment is fully deleted via wp_delete_attachment() which handles:
     * - The attachment post (wp_posts post_type=attachment)
     * - Attachment metadata (wp_postmeta _wp_attachment_metadata, _wp_attached_file)
     * - Physical files on disk (original + all generated sizes)
     *
     * @param int  $product_id The WooCommerce product (post) ID.
     * @param bool $force_delete Whether to bypass trash (true = permanent delete).
     */
    /**
     * WooCommerce placeholder image attachment ID.
     * This shared image must never be deleted by product media cleanup.
     */
    const PLACEHOLDER_IMAGE_ID = 204304;

    public static function delete_product_media(int $product_id, bool $force_delete = true): void
    {
        if ($product_id <= 0) {
            return;
        }

        // 1. Delete featured image (skip if it's the shared placeholder)
        $featured_id = get_post_thumbnail_id($product_id);
        if ($featured_id) {
            if ((int) $featured_id !== self::PLACEHOLDER_IMAGE_ID) {
                wp_delete_attachment((int) $featured_id, $force_delete);
            }
            delete_post_thumbnail($product_id);
        }

        // 2. Delete gallery images (skip placeholder)
        $gallery_ids_str = get_post_meta($product_id, '_product_image_gallery', true);
        if (!empty($gallery_ids_str)) {
            $gallery_ids = array_filter(array_map('intval', explode(',', $gallery_ids_str)));
            foreach ($gallery_ids as $img_id) {
                if ($img_id !== self::PLACEHOLDER_IMAGE_ID) {
                    wp_delete_attachment($img_id, $force_delete);
                }
            }
            delete_post_meta($product_id, '_product_image_gallery');
        }

        // 3. Delete monroney sticker / window sticker attachment
        $monroney_url = get_post_meta($product_id, 'monroney_sticker', true);
        if (!empty($monroney_url)) {
            // Try attachment_url_to_postid first (fast, works for media library items)
            $monroney_id = attachment_url_to_postid($monroney_url);

            // Fallback: query by guid (for externally-sideloaded files)
            if (!$monroney_id) {
                global $wpdb;
                $monroney_id = (int) $wpdb->get_var(
                    $wpdb->prepare(
                        "SELECT ID FROM {$wpdb->posts}
                         WHERE guid = %s AND post_type = 'attachment'
                         LIMIT 1",
                        $monroney_url
                    )
                );
            }

            if ($monroney_id) {
                wp_delete_attachment($monroney_id, $force_delete);
            }
            delete_post_meta($product_id, 'monroney_sticker');
        }

        // 4. Delete any remaining attachments parented to this product
        //    (catches videos, 360 views, manuals, spec sheets, etc.)
        $remaining_attachments = get_posts([
            'post_type'      => 'attachment',
            'post_parent'    => $product_id,
            'posts_per_page' => -1,
            'post_status'    => 'any',
            'fields'         => 'ids',
        ]);
        foreach ($remaining_attachments as $att_id) {
            wp_delete_attachment((int) $att_id, $force_delete);
        }
    }

    /**
     * Fully delete a WooCommerce product and all its associated data.
     *
     * This performs a hard delete, removing:
     * 1. All media attachments (images, gallery, monroney, etc.)
     * 2. WooCommerce lookup table row
     * 3. WooCommerce product transients/caches
     * 4. The product post itself (and all its postmeta)
     *
     * Use this when a cart is sold (isInStock != true) or needs to be
     * removed from the website entirely.
     *
     * @param int $product_id The WooCommerce product (post) ID.
     * @return bool True if product was deleted, false if not found.
     */
    public static function delete_product(int $product_id): bool
    {
        $post = get_post($product_id);
        if (!$post) {
            return false;
        }

        // 1. Delete all media attachments
        self::delete_product_media($product_id, true);

        // 2. Clean WooCommerce lookup table row
        global $wpdb;
        $wpdb->delete(
            $wpdb->prefix . 'wc_product_meta_lookup',
            ['product_id' => $product_id]
        );

        // 3. Clear WooCommerce transients/caches
        if (function_exists('wc_delete_product_transients')) {
            wc_delete_product_transients($product_id);
        }
        clean_post_cache($product_id);

        // Purge the WP Rocket cache while the post still exists, so its
        // permalink and archive URLs can still be resolved. Deferred
        // automatically during a bulk resync.
        Cache::purge_product($product_id, true);

        // 4. Delete the product post (this also deletes all postmeta via WP core)
        $result = wp_delete_post($product_id, true); // true = bypass trash

        return $result !== false && $result !== null;
    }

    /**
     * Ensure WordPress media functions are loaded.
     *
     * Required before any media operations (download, attach, delete).
     * Safe to call multiple times.
     */
    public static function require_media_functions(): void
    {
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }
}
