<?php
/**
 * WP Rocket cache purging.
 *
 * Inventory pages are served from WP Rocket's full-page cache. Whenever the
 * DMS sync adds, updates, or removes a product, the affected cache must be
 * purged so visitors never see stale inventory.
 *
 * Every WP Rocket call is guarded with function_exists() so the plugin runs
 * safely in environments where WP Rocket is inactive (staging, dev, disabled
 * for testing).
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
     * window so thousands of individual rocket_clean_post() calls collapse
     * into a single rocket_clean_domain() when the window closes.
     *
     * @var bool
     */
    private static $deferred = false;

    /**
     * Number of purges suppressed during the current bulk window. end_bulk()
     * uses this so a full-domain purge only fires when something changed.
     *
     * @var int
     */
    private static $deferred_count = 0;

    /**
     * Purge the WP Rocket cache for a single product.
     *
     * No-op while a bulk window is open — end_bulk() purges the whole domain
     * once instead, which is far cheaper than thousands of per-post purges.
     *
     * @param int  $product_id   WooCommerce product (post) ID.
     * @param bool $include_home Also purge the homepage / inventory feed.
     *                           Use on create and delete, not on plain updates.
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

        if (function_exists('rocket_clean_post')) {
            rocket_clean_post($product_id);
        }

        if ($include_home && function_exists('rocket_clean_home')) {
            rocket_clean_home();
        }
    }

    /**
     * Purge the WP Rocket cache for a taxonomy term archive.
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
    }

    /**
     * Purge the entire site cache.
     *
     * Expensive — only call once after a full DMS resync, never inside a
     * per-product loop.
     */
    public static function purge_site(): void
    {
        if (function_exists('rocket_clean_domain')) {
            rocket_clean_domain();
        }
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
     * Close the bulk window and purge the whole domain once — but only if at
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
}
