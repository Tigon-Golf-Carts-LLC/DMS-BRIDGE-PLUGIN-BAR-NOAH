<?php

namespace Tigon\DmsConnect\Includes;

class Product_Fields
{
    /**
     * Define constants
     * 
     * @return none
     */
    public static function define_constants()
    {
        if(!defined('ALL_FIELDS')) {
            define('NAME', 1 << 0);
            define('SHORT_DESCRIPTION', 1 << 1);
            define('DESCRIPTION', 1 << 2);
            define('PUBLISHED', 1 << 3);
            define('COMMENT_STATUS', 1 << 4);
            define('PING_STATUS', 1 << 5);
            define('MENU_ORDER', 1 << 6);
            define('POST_TYPE', 1 << 7);
            define('COMMENT_COUNT', 1 << 8);
            define('POST_AUTHOR', 1 << 9);
            define('SLUG', 1 << 10);
            define('SKU', 1 << 11);
            define('TAX_STATUS', 1 << 12);
            define('TAX_CLASS', 1 << 13);
            define('MANAGE_STOCK', 1 << 14);
            define('BACKORDERS_ALLOWED', 1 << 15);
            define('SOLD_INDIVIDUALLY', 1 << 16);
            define('IS_VIRTUAL', 1 << 17);
            define('DOWNLOADABLE', 1 << 18);
            define('DOWNLOAD_LIMIT', 1 << 19);
            define('DOWNLOAD_EXPIRY', 1 << 20);
            define('STOCK', 1 << 21);
            define('IN_STOCK', 1 << 22);
            define('GUI', 1 << 23);
            define('IMAGES', 1 << 24);
            define('PRICE', 1 << 25);
            define('SALE_PRICE', 1 << 26);
            define('YOAST_SEO_TITLE', 1 << 27);
            define('META_DESCRIPTION', 1 << 28);
            define('PRIMARY_CATEGORY', 1 << 29);
            define('PRIMARY_LOCATION', 1 << 30);
            define('PRIMARY_MODEL', 1 << 31);
            define('PRIMARY_ADDED_FEATURE', 1 << 32);
            define('BIT_IS_CORNERSTONE', 1 << 33);
            define('CUSTOM_TABS', 1 << 34);
            define('ATTR_EXCLUDE_GLOBAL_FORMS', 1 << 35);
            define('CUSTOM_PRODUCT_OPTIONS', 1 << 36);
            define('CONDITION', 1 << 37);
            define('GOOGLE_BRAND', 1 << 38);
            define('GOOGLE_COLOR', 1 << 39);
            define('GOOGLE_PATTERN', 1 << 40);
            define('GENDER', 1 << 41);
            define('GOOGLE_SIZE_SYSTEM', 1 << 42);
            define('ADULT_CONTENT', 1 << 43);
            define('GOOGLE_CATEGORY', 1 << 44);
            define('AGE_GROUP', 1 << 45);
            define('PRODUCT_IMAGE_SOURCE', 1 << 46);
            define('FACEBOOK_SYNC', 1 << 47);
            define('FACEBOOK_VISIBILITY', 1 << 48);
            define('MONRONEY_STICKER', 1 << 49);
            define('MONRONEY_CONTAINER_ID', 1 << 50);
            define('TIGONWM_TEXT', 1 << 51);
            define('TAXONOMY_TERMS', 1 << 52);

            define('ALL_FIELDS', (1 << 53) - 1);
        }
    }

    /**
     * Get all defined options.
     *
     * @return array
     */
    public static function get_options(): array
    {
        self::define_constants();
        return [
            NAME => 'name',
            SHORT_DESCRIPTION => 'short_description',
            DESCRIPTION => 'description',
            PUBLISHED => 'published',
            COMMENT_STATUS => 'comment_status',
            PING_STATUS => 'ping_status',
            MENU_ORDER => 'menu_order',
            POST_TYPE => 'post_type',
            COMMENT_COUNT => 'comment_count',
            POST_AUTHOR => 'post_author',
            SLUG => 'slug',
            SKU => 'sku',
            TAX_STATUS => 'tax_status',
            TAX_CLASS => 'tax_class',
            MANAGE_STOCK => 'manage_stock',
            BACKORDERS_ALLOWED => 'backorders_allowed',
            SOLD_INDIVIDUALLY => 'sold_individually',
            IS_VIRTUAL => 'is_virtual',
            DOWNLOADABLE => 'downloadable',
            DOWNLOAD_LIMIT => 'download_limit',
            DOWNLOAD_EXPIRY => 'download_expiry',
            STOCK => 'stock',
            IN_STOCK => 'in_stock',
            GUI => 'gui',
            IMAGES => 'images',
            PRICE => 'price',
            SALE_PRICE => 'sale_price',
            YOAST_SEO_TITLE => 'yoast_seo_title',
            META_DESCRIPTION => 'meta_description',
            PRIMARY_CATEGORY => 'primary_category',
            PRIMARY_LOCATION => 'primary_location',
            PRIMARY_MODEL => 'primary_model',
            PRIMARY_ADDED_FEATURE => 'primary_added_feature',
            BIT_IS_CORNERSTONE => 'bit_is_cornerstone',
            CUSTOM_TABS => 'custom_tabs',
            ATTR_EXCLUDE_GLOBAL_FORMS => 'attr_exclude_global_forms',
            CUSTOM_PRODUCT_OPTIONS => 'custom_product_options',
            CONDITION => 'condition',
            GOOGLE_BRAND => 'google_brand',
            GOOGLE_COLOR => 'google_color',
            GOOGLE_PATTERN => 'google_pattern',
            GENDER => 'gender',
            GOOGLE_SIZE_SYSTEM => 'google_size_system',
            ADULT_CONTENT => 'adult_content',
            GOOGLE_CATEGORY => 'google_category',
            AGE_GROUP => 'age_group',
            PRODUCT_IMAGE_SOURCE => 'product_image_source',
            FACEBOOK_SYNC => 'facebook_sync',
            FACEBOOK_VISIBILITY => 'facebook_visibility',
            MONRONEY_STICKER => 'monroney_sticker',
            MONRONEY_CONTAINER_ID => 'monroney_container_id',
            TIGONWM_TEXT => 'tigonwm_text',
            TAXONOMY_TERMS => 'taxonomy_terms'
        ];
    }

    /**
     * Check if a specific option is set in a given binary value.
     *
     * @param int $value
     * @param int $option
     * @return bool
     */
    public static function has_option(int $value, int $option): bool
    {
        return ($value & $option) === $option;
    }

    /**
     * Combine multiple options into a single binary value.
     *
     * @param int ...$options
     * @return int
     */
    public static function combine_options(int ...$options): int
    {
        $result = 0;
        foreach ($options as $option) {
            $result |= $option;
        }
        return $result;
    }
}
