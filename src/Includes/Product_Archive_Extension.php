<?php

namespace Tigon\DmsConnect\Includes;

use Automattic\WooCommerce\Admin\API\Reports\Categories\Query;
use WP_Query;

class Product_Archive_Extension
{
    public static function get_list_from_db($location_code, $manufacturer_code) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'tigon_dms_cart_lists';

        if(!$manufacturer_code) {
            return array_merge(
                self::get_list_from_db($location_code, 'featuredNewCarts'),
                self::get_list_from_db($location_code, 'featuredUsedCarts')
            );
        } else {
            $list = $wpdb->get_var(
                'SELECT list FROM ' . $table_name .
                ' WHERE location_name = "' . $location_code . '"' .
                'AND list_name = "' . $manufacturer_code . '";'
            );

            return json_decode($list ?? '[]');
        }
    }

    public static function modify_sort_by_price()
    {
        $orderby = $_REQUEST['pf_filters'][$_REQUEST['pf_id'] ?? null]['orderby'] ?? null;
        if ( isset( $_REQUEST['pf_id'] ) && isset($orderby) ) {
            if ( $orderby == 'price' || $orderby == 'price-desc' ) {
                add_filter( 'posts_clauses', 'Tigon\DmsConnect\Includes\Product_Archive_Extension::modify_posts_clauses',999999,2 );                
            }
        } 
    }

    public static function modify_posts_clauses($args) 
    {
        if(str_contains($args['join'], 'wc_product_meta_lookup')) {
            $orderby = $_REQUEST['pf_filters'][$_REQUEST['pf_id'] ?? null]['orderby'] ?? null;
            global $wpdb;
            if ($orderby == 'price' ) {
                $args['orderby'] = $wpdb->prefix.'posts.menu_order ASC, wc_product_meta_lookup.min_price ASC ';               
            }

            if ($orderby == 'price-desc' ) {
                $args['orderby'] = $wpdb->prefix.'posts.menu_order ASC, wc_product_meta_lookup.max_price DESC ';
            }
        }
        return $args;
    }

    /**
     * Fetch static set of products for the first page
     */
    public static function custom_order_products($query)
    {
        global $prdctfltr_global;

        if (wp_doing_ajax() && isset($prdctfltr_global['active_filters'])) {

            $location = $prdctfltr_global['active_filters']['vehicle-location'] ?? [''];
            switch($location[0] ?? '') {
                case 'hatfield':
                case 'pocono':
                case 'dover':
                case 'ocean-view':
                case 'scranton':
                    $location_code = 'tigon-'.$location[0];
                    break;
                default:
                    $location_code = 'national';
                    break;
            }

            $manufacturer = $prdctfltr_global['active_filters']['manufacturers'] ?? [''];
            switch($manufacturer[0] ?? '') {
                case 'epic':
                case 'evolution':
                case 'denago':
                case 'icon':
                    $manufacturer_code = $manufacturer[0];
                    break;
                case 'swift':
                    $manufacturer_code = 'swift-ev';
                    break;
                default:
                    $manufacturer_code = null;
                    break;
            }

            $location_code = str_replace('-', '_', $location_code);
            $manufacturer_code = str_replace('-', '_', $manufacturer_code);

            $prioritized_ids = self::get_list_from_db($location_code, $manufacturer_code);
            $prioritized_ids = array_reverse($prioritized_ids);
            $prioritized_ids = array_unique($prioritized_ids, SORT_REGULAR);
            $prioritized_ids = array_filter($prioritized_ids);

            // If we have prioritized products, add them first to the query
            if (!empty($prioritized_ids)) {
                add_filter('posts_orderby', function ($orderby_statement) use ($prioritized_ids) {
                    $orderby_statement = 'FIELD(ID,' . implode(',', $prioritized_ids) . ') DESC, menu_order ASC, post_title ASC';
                    return $orderby_statement;
                }, 999999);
            }
        }

        if ((!is_admin() && is_archive() && $query->get('posts_per_page') === '16')) {
            $location = $query->get('vehicle-location') ?? [''];
            switch($location[0] ?? '') {
                case 'hatfield':
                case 'pocono':
                case 'dover':
                case 'ocean-view':
                case 'scranton':
                    $location_code = 'tigon-'.$location[0];
                    break;
                default:
                    $location_code = 'national';
                    break;
            }

            $manufacturer = $query->get('manufacturers') ?? [''];
            switch($manufacturer[0] ?? '') {
                case 'epic':
                case 'evolution':
                case 'denago':
                case 'icon':
                    $manufacturer_code = $manufacturer[0];
                    break;
                case 'swift':
                    $manufacturer_code = 'swift-ev';
                    break;
                default:
                    $manufacturer_code = null;
                    break;
            }

            $location_code = str_replace('-', '_', $location_code);
            $manufacturer_code = str_replace('-', '_', $manufacturer_code);

            $prioritized_ids = self::get_list_from_db($location_code, $manufacturer_code);;
            $prioritized_ids = array_reverse($prioritized_ids);
            $prioritized_ids = array_unique($prioritized_ids, SORT_REGULAR);
            $prioritized_ids = array_filter($prioritized_ids);

            // If we have prioritized products, add them first to the query
            global $wpdb;
            if (!empty($prioritized_ids)) {
                add_filter('posts_orderby', function ($orderby_statement) use ($wpdb, $prioritized_ids) {
                    $orderby_statement = 'FIELD('.$wpdb->prefix.'posts.ID,' . implode(',', $prioritized_ids) . ') DESC, '.$wpdb->prefix.'posts.menu_order ASC, '.$wpdb->prefix.'posts.post_title ASC';
                    return $orderby_statement;
                }, 999999);
            }
        }

        return $query;
    }
}
