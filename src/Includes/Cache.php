<?php
/**
 * Cache purging — two layers.
 *
 * Inventory pages are served from a full-page cache (WP Rocket at the origin)
 * and a CDN edge cache (Cloudflare). Whenever the DMS sync adds, updates, or
 * removes a product, both layers must be invalidated so visitors never see
 * stale inventory.
 *
 * This helper is the single entry point: every DMS write path calls
 * purge_product() / purge_term() / purge_site(), and it fans out to WP Rocket
 * (immediate, filesystem) and Cloudflare (coalesced, see the Cloudflare class).
 *
 * WP Rocket calls are guarded with function_exists() so the plugin runs safely
 * where WP Rocket is inactive (staging, dev, disabled for testing). Cloudflare
 * calls are skipped unless credentials are configured.
 *
 * @package Tigon\DmsConnect\Includes
 */

namespace Tigon\DmsConnect\Includes;

if (!defined('ABSPATH')) {
    exit;
}

class Cache
{
    /**
     * While true, per-product purges are deferred. A bulk resync opens this
     * window so thousands of individual purges collapse into a single
     * full-cache purge when the window closes.
     *
     * @var bool
     */
    private static $deferred = false;

    /**
     * Number of purges suppressed during the current bulk window. end_bulk()
     * uses this so a full purge only fires when something changed.
     *
     * @var int
     */
    private static $deferred_count = 0;

    /**
     * Purge the cache for a single product.
     *
     * No-op while a bulk window is open — end_bulk() purges the whole site
     * once instead, which is far cheaper than thousands of per-post purges.
     *
     * @param int  $product_id   WooCommerce product (post) ID.
     * @param bool $include_home Also purge the homepage via WP Rocket. Use on
     *                           create and delete, not on plain updates. (The
     *                           Cloudflare URL set always includes the home
     *                           and inventory listing — they dedupe per flush.)
     */
    public static function purge_product(int $product_id, bool $include_home = false): void
    {
        if ($product_id <= 0) {
            return;
        }

        if (self::$deferred) {
            self::$deferred_count++;
            return;
        }

        // Layer 1 — WP Rocket origin full-page cache.
        if (function_exists('rocket_clean_post')) {
            rocket_clean_post($product_id);
        }
        if ($include_home && function_exists('rocket_clean_home')) {
            rocket_clean_home();
        }

        // Layer 2 — Cloudflare edge cache. URLs are coalesced into a single
        // scheduled flush so high update volume stays within API rate limits.
        if (Cloudflare::is_configured()) {
            Cloudflare::queue_urls(self::product_urls($product_id));
        }
    }

    /**
     * Purge the cache for a taxonomy term archive.
     *
     * No-op while a bulk window is open.
     *
     * @param int    $term_id  Term ID.
     * @param string $taxonomy Taxonomy name, e.g. 'product_cat' or 'product_tag'.
     */
    public static function purge_term(int $term_id, string $taxonomy): void
    {
        if ($term_id <= 0 || $taxonomy === '') {
            return;
        }

        if (self::$deferred) {
            self::$deferred_count++;
            return;
        }

        if (function_exists('rocket_clean_term')) {
            rocket_clean_term($term_id, $taxonomy);
        }

        if (Cloudflare::is_configured()) {
            $link = get_term_link($term_id, $taxonomy);
            if (!is_wp_error($link)) {
                Cloudflare::queue_urls([$link]);
            }
        }
    }

    /**
     * Purge the entire site cache, both layers.
     *
     * Expensive — only call once after a full DMS resync, never inside a
     * per-product loop.
     */
    public static function purge_site(): void
    {
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }

        Cloudflare::purge_everything_now();
    }

    /**
     * Open a bulk window: defer per-product purges until end_bulk().
     */
    public static function begin_bulk(): void
    {
        self::$deferred       = true;
        self::$deferred_count = 0;
    }

    /**
     * Close the bulk window and purge the whole site once — but only if at
     * least one product purge was deferred while the window was open.
     */
    public static function end_bulk(): void
    {
        $had_changes          = self::$deferred_count > 0;
        self::$deferred       = false;
        self::$deferred_count = 0;

        if ($had_changes) {
            self::purge_site();
        }
    }

    /**
     * Whether a bulk window is currently open.
     */
    public static function is_deferred(): bool
    {
        return self::$deferred;
    }

    /**
     * Build the list of edge URLs affected by a change to one product: the
     * product page, the homepage, the inventory listing, and the product's
     * category and tag archives.
     *
     * @param int $product_id WooCommerce product (post) ID.
     * @return string[]
     */
    private static function product_urls(int $product_id): array
    {
        $urls = [];

        $permalink = get_permalink($product_id);
        if ($permalink) {
            $urls[] = $permalink;
        }

        $urls[] = home_url('/');
        $urls[] = home_url('/inventory/');

        foreach (['product_cat', 'product_tag'] as $taxonomy) {
            $term_ids = wp_get_post_terms($product_id, $taxonomy, ['fields' => 'ids']);
            if (is_wp_error($term_ids)) {
                continue;
            }
            foreach ($term_ids as $term_id) {
                $link = get_term_link((int) $term_id, $taxonomy);
                if (!is_wp_error($link)) {
                    $urls[] = $link;
                }
            }
        }

        return $urls;
    }
}
