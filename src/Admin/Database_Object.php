<?php

namespace Tigon\DmsConnect\Admin;

class Database_Object
{
    // Database array
    public $data = [
        '_id' => '',
        'method' => '',
        'posts' => [],
        'postmeta' => [
            // WooCommerce
            '_sku' => ['meta_key' => '_sku'],
            '_tax_status' => ['meta_key' => '_tax_status'],
            '_tax_class' => ['meta_key' => '_tax_class'],
            '_manage_stock' => ['meta_key' => '_manage_stock'],
            '_backorders' => ['meta_key' => '_backorders'],
            '_sold_individually' => ['meta_key' => '_sold_individually'],
            '_virtual' => ['meta_key' => '_virtual'],
            '_downloadable' => ['meta_key' => '_downloadable'],
            '_download_limit' => ['meta_key' => '_download_limit'],
            '_download_expiry' => ['meta_key' => '_download_expiry'],
            '_stock' => ['meta_key' => '_stock'],
            '_stock_status' => ['meta_key' => '_stock_status'],
            '_global_unique_id' => ['meta_key' => '_global_unique_id'],
            '_product_attributes' => ['meta_key' => '_product_attributes'],
            '_thumbnail_id' => ['meta_key' => '_thumbnail_id'],
            '_product_image_gallery' => ['meta_key' => '_product_image_gallery'],
            '_regular_price' => ['meta_key' => '_regular_price'],
            '_sale_price' => ['meta_key' => '_sale_price'],
            '_price' => ['meta_key' => '_price'],
            '_weight' => ['meta_key' => '_weight'],
            '_length' => ['meta_key' => '_length'],
            '_width' => ['meta_key' => '_width'],
            '_height' => ['meta_key' => '_height'],
            // Yoast SEO
            '_yoast_wpseo_title' => ['meta_key' => '_yoast_wpseo_title'],
            '_yoast_wpseo_metadesc' => ['meta_key' => '_yoast_wpseo_metadesc'],
            '_yoast_wpseo_primary_product_cat' => ['meta_key' => '_yoast_wpseo_primary_product_cat'],
            '_yoast_wpseo_primary_location' => ['meta_key' => '_yoast_wpseo_primary_location'],
            '_yoast_wpseo_primary_models' => ['meta_key' => '_yoast_wpseo_primary_models'],
            '_yoast_wpseo_primary_added-features' => ['meta_key' => '_yoast_wpseo_primary_added-features'],
            '_yoast_wpseo_is_cornerstone' => ['meta_key' => '_yoast_wpseo_is_cornerstone'],
            '_yoast_wpseo_focus_kw' => ['meta_key' => '_yoast_wpseo_focus_kw'],
            '_yoast_wpseo_focus_keywords' => ['meta_key' => '_yoast_wpseo_focus_keywords'],
            '_yoast_wpseo_bctitle' => ['meta_key' => '_yoast_wpseo_bctitle'],
            '_yoast_wpseo_opengraph-title' => ['meta_key' => '_yoast_wpseo_opengraph-title'],
            '_yoast_wpseo_opengraph-description' => ['meta_key' => '_yoast_wpseo_opengraph-description'],
            '_yoast_wpseo_opengraph-image-id' => ['meta_key' => '_yoast_wpseo_opengraph-image-id'],
            '_yoast_wpseo_opengraph-image' => ['meta_key' => '_yoast_wpseo_opengraph-image'],
            '_yoast_wpseo_twitter-image-id' => ['meta_key' => '_yoast_wpseo_twitter-image-id'],
            '_yoast_wpseo_twitter-image' => ['meta_key' => '_yoast_wpseo_twitter-image'],
            // Product tabs
            '_yikes_woo_products_tabs' => ['meta_key' => '_yikes_woo_products_tabs'],
            // Custom product addons
            'wcpa_exclude_global_forms' => ['meta_key' => 'wcpa_exclude_global_forms'],
            '_wcpa_product_meta' => ['meta_key' => '_wcpa_product_meta'],
            // Google for WooCommerce
            '_wc_gla_mpn' => ['meta_key' => '_wc_gla_mpn'],
            '_wc_gla_condition' => ['meta_key' => '_wc_gla_condition'],
            '_wc_gla_brand' => ['meta_key' => '_wc_gla_brand'],
            '_wc_gla_color' => ['meta_key' => '_wc_gla_color'],
            '_wc_gla_pattern' => ['meta_key' => '_wc_gla_pattern'],
            '_wc_gla_gender' => ['meta_key' => '_wc_gla_gender'],
            '_wc_gla_sizeSystem' => ['meta_key' => '_wc_gla_sizeSystem'],
            '_wc_gla_adult' => ['meta_key' => '_wc_gla_adult'],
            // Pinterest for WooCommerce
            '_wc_pinterest_condition' => ['meta_key' => '_wc_pinterest_condition'],
            '_wc_pinterest_google_product_category' => ['meta_key' => '_wc_pinterest_google_product_category'],
            // Facebook for WooCommerce
            '_wc_facebook_enhanced_catalog_attributes_brand' => ['meta_key' => '_wc_facebook_enhanced_catalog_attributes_brand'],
            '_wc_facebook_enhanced_catalog_attributes_color' => ['meta_key' => '_wc_facebook_enhanced_catalog_attributes_color'],
            '_wc_facebook_enhanced_catalog_attributes_pattern' => ['meta_key' => '_wc_facebook_enhanced_catalog_attributes_pattern'],
            '_wc_facebook_enhanced_catalog_attributes_gender' => ['meta_key' => '_wc_facebook_enhanced_catalog_attributes_gender'],
            '_wc_facebook_enhanced_catalog_attributes_age_group' => ['meta_key' => '_wc_facebook_enhanced_catalog_attributes_age_group'],
            '_wc_facebook_product_image_source' => ['meta_key' => '_wc_facebook_product_image_source'],
            '_wc_facebook_sync_enabled' => ['meta_key' => '_wc_facebook_sync_enabled'],
            '_wc_fb_visibility' => ['meta_key' => '_wc_fb_visibility'],
            // Tigon specific
            '_dms_cart_id' => ['meta_key' => '_dms_cart_id'],
            '_dms_readiness_missing' => ['meta_key' => '_dms_readiness_missing'],
            'monroney_sticker' => ['meta_key' => 'monroney_sticker'],
            '_monroney_sticker' => ['meta_key' => '_monroney_sticker'],
            'tigonwm' => ['meta_key' => '_tigonwm'],
        ],
        'term_relationships' => []
    ];

    public function __construct(
        ?string $method = null,
        ?int $id = null,
        ?string $name = null,
        ?string $short_description = null,
        ?string $description = null,
        ?string $published = null,
        ?string $comment_status = null,
        ?string $ping_status = null,
        ?string $menu_order = null,
        ?string $post_type = null,
        ?string $comment_count = null,
        ?string $post_author = null,
        ?string $slug = null,

        ?string $sku = null,
        ?string $tax_status = null,
        ?string $tax_class = null,
        ?string $manage_stock = null,
        ?string $backorders_allowed = null,
        ?string $sold_individually = null,
        ?string $is_virtual = null,
        ?string $downloadable = null,
        ?string $download_limit = null,
        ?string $download_expiry = null,
        ?string $stock = null,
        ?string $in_stock = null,
        ?string $gui = null,
        ?string $attributes = null,
        ?array $images = null,
        ?string $price = null,
        ?string $sale_price = null,
 
        ?string $yoast_seo_title = null,
        ?string $meta_description = null,
        ?string $primary_category = null,
        ?string $primary_location = null,
        ?string $primary_model = null,
        ?string $primary_added_feature = null,
        ?string $bit_is_cornerstone = null,

        ?string $custom_tabs = null,

        ?string $attr_exclude_global_forms = null,
        ?string $custom_product_options = null,

        ?string $condition = null,
        ?string $google_brand = null,
        ?string $google_color = null,
        ?string $google_pattern = null,
        ?string $gender = null,
        ?string $google_size_system = null,
        ?string $adult_content = null,
        ?string $google_category = null,
        ?string $age_group = null,
        ?string $product_image_source = null,
        ?string $facebook_sync = null,
        ?string $facebook_visibility = null,

        ?string $dms_cart_id = null,
        ?string $dms_readiness_missing = null,
        ?string $monroney_sticker = null,
        ?string $monroney_container_id = null,
        ?string $tigonwm_text = null,

        ?array $taxonomy_terms = null
    ) {
        // Generate Global Trade Id Number
        $gui = substr(implode(array_map(function ($char) {
            $int = ord($char);
            if ($int - 65 >= 0) {
                return (($int - 65) % 9) + 1;
            } else return $char;
        }, str_split($sku))), -14, 14);
        $gui = str_pad($gui, 14, '0', STR_PAD_LEFT);

        // Get first image as main image; fall back to placeholder (ID 204304)
        $images = $images??[];
        $featured_image = array_shift($images);
        if (empty($featured_image)) {
            $featured_image = 204304;
        }
        $featured_image_url = wp_get_attachment_image_url($featured_image);
        $images_list = implode(',', $images);

        if (!empty($method)) $this->data['method'] = $method;

        // Fill posts table data
        if (!empty($id)) $this->data['posts']['ID'] = $id;
        if (!empty($name)) $this->data['posts']['post_title'] = $name;
        if (!empty($short_description)) $this->data['posts']['post_excerpt'] = $short_description;
        if (!empty($description)) $this->data['posts']['post_content'] = $description;
        if (!empty($published)) $this->data['posts']['post_status'] = $published;
        if (!empty($comment_status)) $this->data['posts']['comment_status'] = $comment_status;
        if (!empty($ping_status)) $this->data['posts']['ping_status'] = $ping_status;
        if (!empty($menu_order)) $this->data['posts']['menu_order'] = $menu_order;
        if (!empty($post_type)) $this->data['posts']['post_type'] = $post_type;
        if (!empty($comment_count)) $this->data['posts']['comment_count'] = $comment_count;
        if (!empty($post_author)) $this->data['posts']['post_author'] = $post_author;
        if (!empty($slug)) $this->data['posts']['post_name'] = $slug;

        // Fill postmeta table data
        // WooCommerce
        if (!empty($sku)) $this->data['postmeta']['_sku']['meta_value'] = $sku;
        if (!empty($tax_status)) $this->data['postmeta']['_tax_status']['meta_value'] = $tax_status;
        if (!empty($tax_class)) $this->data['postmeta']['_tax_class']['meta_value'] = $tax_class;
        if (!empty($manage_stock)) $this->data['postmeta']['_manage_stock']['meta_value'] = $manage_stock;
        if (!empty($backorders_allowed)) $this->data['postmeta']['_backorders']['meta_value'] = $backorders_allowed;
        if (!empty($sold_individually)) $this->data['postmeta']['_sold_individually']['meta_value'] = $sold_individually;
        if (!empty($is_virtual)) $this->data['postmeta']['_virtual']['meta_value'] = $is_virtual;
        if (!empty($downloadable)) $this->data['postmeta']['_downloadable']['meta_value'] = $downloadable;
        if (!empty($download_limit)) $this->data['postmeta']['_download_limit']['meta_value'] = $download_limit;
        if (!empty($download_expiry)) $this->data['postmeta']['_download_expiry']['meta_value'] = $download_expiry;
        if (!empty($stock)) $this->data['postmeta']['_stock']['meta_value'] = $stock;
        if (!empty($in_stock)) $this->data['postmeta']['_stock_status']['meta_value'] = $in_stock;
        if (!empty($gui)) $this->data['postmeta']['_global_unique_id']['meta_value'] = $gui;
        if (!empty($attributes)) $this->data['postmeta']['_product_attributes']['meta_value'] = $attributes;
        if (!empty($featured_image)) $this->data['postmeta']['_thumbnail_id']['meta_value'] = $featured_image;
        if (!empty($images_list)) $this->data['postmeta']['_product_image_gallery']['meta_value'] = $images_list;
        // Skip price writes entirely when the Pricing admin page is
        // managing this product's price (marker meta: _tigon_price_source).
        $price_managed = ($id && function_exists('tigon_dms_is_price_managed'))
            ? tigon_dms_is_price_managed($id)
            : false;
        if (!$price_managed) {
            if ($price !== null && $price !== '' && floatval($price) > 0) {
                $formatted_price = number_format(floatval($price), 2, '.', '');
                $this->data['postmeta']['_regular_price']['meta_value'] = $formatted_price;
                $this->data['postmeta']['_price']['meta_value'] = $formatted_price;
            }
            if ($sale_price !== null && $sale_price !== '' && floatval($sale_price) > 0 && floatval($sale_price) < floatval($price)) {
                $formatted_sale = number_format(floatval($sale_price), 2, '.', '');
                $this->data['postmeta']['_sale_price']['meta_value'] = $formatted_sale;
                $this->data['postmeta']['_price']['meta_value'] = $formatted_sale;
            }
        }
        // Shipping dimensions (placeholder for golf carts)
        $this->data['postmeta']['_weight']['meta_value'] = '500';
        $this->data['postmeta']['_length']['meta_value'] = '96';
        $this->data['postmeta']['_width']['meta_value'] = '48';
        $this->data['postmeta']['_height']['meta_value'] = '72';
        // Yoast SEO
        if (!empty($yoast_seo_title)) $this->data['postmeta']['_yoast_wpseo_title']['meta_value'] = $yoast_seo_title;
        if (!empty($meta_description)) $this->data['postmeta']['_yoast_wpseo_metadesc']['meta_value'] = $meta_description;
        if (!empty($primary_category)) $this->data['postmeta']['_yoast_wpseo_primary_product_cat']['meta_value'] = $primary_category;
        if (!empty($primary_location)) $this->data['postmeta']['_yoast_wpseo_primary_location']['meta_value'] = $primary_location;
        if (!empty($primary_model)) $this->data['postmeta']['_yoast_wpseo_primary_models']['meta_value'] = $primary_model;
        if (!empty($primary_added_feature)) $this->data['postmeta']['_yoast_wpseo_primary_added-features']['meta_value'] = $primary_added_feature;
        if (!empty($bit_is_cornerstone)) $this->data['postmeta']['_yoast_wpseo_is_cornerstone']['meta_value'] = $bit_is_cornerstone;
        if (!empty($name)) $this->data['postmeta']['_yoast_wpseo_focus_kw']['meta_value'] = $name;
        if (!empty($name)) $this->data['postmeta']['_yoast_wpseo_focus_keywords']['meta_value'] = $name;
        if (!empty($name)) $this->data['postmeta']['_yoast_wpseo_bctitle']['meta_value'] = $name;
        if (!empty($name)) $this->data['postmeta']['_yoast_wpseo_opengraph-title']['meta_value'] = $name;
        if (!empty($meta_description)) $this->data['postmeta']['_yoast_wpseo_opengraph-description']['meta_value'] = $meta_description;
        if (!empty($featured_image)) $this->data['postmeta']['_yoast_wpseo_opengraph-image-id']['meta_value'] = $featured_image;
        if (!empty($featured_image) && $featured_image_url !== false) $this->data['postmeta']['_yoast_wpseo_opengraph-image']['meta_value'] = $featured_image_url;
        if (!empty($featured_image)) $this->data['postmeta']['_yoast_wpseo_twitter-image-id']['meta_value'] = $featured_image;
        if (!empty($featured_image) && $featured_image_url !== false) $this->data['postmeta']['_yoast_wpseo_twitter-image']['meta_value'] = $featured_image_url;
        // Product tabs
        if (!empty($custom_tabs)) $this->data['postmeta']['_yikes_woo_products_tabs']['meta_value'] = $custom_tabs;
        // Custom product addons
        if (!empty($attr_exclude_global_forms)) $this->data['postmeta']['wcpa_exclude_global_forms']['meta_value'] = $attr_exclude_global_forms;
        if (!empty($custom_product_options)) $this->data['postmeta']['_wcpa_product_meta']['meta_value'] = $custom_product_options;
        // Google for WooCommerce
        if (!empty($gui)) $this->data['postmeta']['_wc_gla_mpn']['meta_value'] = $gui;
        if (!empty($condition)) $this->data['postmeta']['_wc_gla_condition']['meta_value'] = $condition;
        if (!empty($google_brand)) $this->data['postmeta']['_wc_gla_brand']['meta_value'] = $google_brand;
        if (!empty($google_color)) $this->data['postmeta']['_wc_gla_color']['meta_value'] = $google_color;
        if (!empty($google_pattern)) $this->data['postmeta']['_wc_gla_pattern']['meta_value'] = $google_pattern;
        if (!empty($gender)) $this->data['postmeta']['_wc_gla_gender']['meta_value'] = $gender;
        if (!empty($google_size_system)) $this->data['postmeta']['_wc_gla_sizeSystem']['meta_value'] = $google_size_system;
        if (!empty($adult_content)) $this->data['postmeta']['_wc_gla_adult']['meta_value'] = $adult_content;
        // Pinterest for WooCommerce
        if (!empty($condition)) $this->data['postmeta']['_wc_pinterest_condition']['meta_value'] = $condition;
        if (!empty($google_category)) $this->data['postmeta']['_wc_pinterest_google_product_category']['meta_value'] = $google_category;
        // Facebook for WooCommerce
        if (!empty($google_brand)) $this->data['postmeta']['_wc_facebook_enhanced_catalog_attributes_brand']['meta_value'] = $google_brand;
        if (!empty($google_color)) $this->data['postmeta']['_wc_facebook_enhanced_catalog_attributes_color']['meta_value'] = $google_color;
        if (!empty($google_pattern)) $this->data['postmeta']['_wc_facebook_enhanced_catalog_attributes_pattern']['meta_value'] = $google_pattern;
        if (!empty($gender)) $this->data['postmeta']['_wc_facebook_enhanced_catalog_attributes_gender']['meta_value'] = $gender;
        if (!empty($age_group)) $this->data['postmeta']['_wc_facebook_enhanced_catalog_attributes_age_group']['meta_value'] = $age_group;
        if (!empty($product_image_source)) $this->data['postmeta']['_wc_facebook_product_image_source']['meta_value'] = $product_image_source;
        if (!empty($facebook_sync)) $this->data['postmeta']['_wc_facebook_sync_enabled']['meta_value'] = $facebook_sync;
        if (!empty($facebook_visibility)) $this->data['postmeta']['_wc_fb_visibility']['meta_value'] = $facebook_visibility;
        // Tigon specific
        if (!empty($dms_cart_id)) $this->data['postmeta']['_dms_cart_id']['meta_value'] = $dms_cart_id;
        // Track missing readiness fields; clear when product is ready
        if (!empty($dms_readiness_missing)) {
            $this->data['postmeta']['_dms_readiness_missing']['meta_value'] = $dms_readiness_missing;
        } else {
            $this->data['postmeta']['_dms_readiness_missing']['meta_value'] = '';
        }
        if (!empty($monroney_sticker)) $this->data['postmeta']['monroney_sticker']['meta_value'] = $monroney_sticker;
        if (!empty($monroney_container_id)) $this->data['postmeta']['_monroney_sticker']['meta_value'] = $monroney_container_id;
        if (!empty($tigonwm_text)) $this->data['postmeta']['tigonwm']['meta_value'] = $tigonwm_text;

        // Fill term_relationships table data
        foreach (array_filter($taxonomy_terms??[]) as $term) {
            array_push(
                $this->data['term_relationships'],
                [
                    'object_id' => $id,
                    'term_taxonomy_id' => $term,
                    'term_order' => 0
                ]
            );
        }
    }

    public function get_value(...$path) : mixed {
        return \Tigon\DmsConnect\Includes\Utilities::array_access($this->data, ...$path);
    }
    
    /**
     * Sets a value in the Database Object
     *
     * @param [type] $table
     * @param [type] $value
     * @param [type] $key
     * @return void
     */
    public function set_value($table, $value, $key = null) {
        switch($table) {
            case 'posts':
                if($key) $this->data['posts'][$key] = $value;
                break;
            case 'postmeta':
                if($key) $this->data['postmeta'][$key]['meta_value'] = $value;
                break;
            case 'term_relationships':
                $this->data['term_relationships'] = $value;
                break;
        }
    }

    public static function save_to_database(Database_Object $database_object) {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $post = $database_object->get_value('posts');
        $wpdb->insert_id;
    }

    /**
     * Gets a Database_Object from the wordpress database by post id
     *
     * @param int $id
     * @return Database_Object
     */
    public static function get_from_wpdb(int $id) : Database_Object {
        $database_object = new Database_Object;

        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $posts = $wpdb->get_row('SELECT * FROM '.$wpdb->prefix.'posts WHERE ID = '.$id.';', ARRAY_A);
        foreach($posts as $column => $value) {
            $database_object->set_value('posts', $value, $column);
        }

        $postmeta = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'postmeta WHERE post_id = '.$id.';', ARRAY_A);
        foreach ($postmeta as $row) {
            $meta_key   = $row['meta_key']   ?? null;
            $meta_value = $row['meta_value'] ?? null;

            // Only map keys that exist in the Database_Object template
            if ($meta_key !== null && array_key_exists($meta_key, $database_object->data['postmeta'])) {
                $database_object->set_value('postmeta', $meta_value, $meta_key);
            }
        }

        $term_relationships = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'term_relationships WHERE object_id = '.$id.';', ARRAY_A);
        $terms = [];
        foreach($term_relationships as $row) {
            array_push($terms, $row['term_taxonomy_id']);
        }
        $database_object->set_value('term_relationships', $terms);

        return $database_object;
    }
}
