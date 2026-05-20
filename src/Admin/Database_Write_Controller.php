<?php

namespace Tigon\DmsConnect\Admin;

final class Database_Write_Controller {
    protected static function write_database_object(Database_Object $database_object) : int {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $posts = $database_object->get_value('posts');

        if(!empty($posts['ID'])) {
            $post_id = $database_object->get_value('posts', 'ID');
            $wpdb->update($wpdb->prefix.'posts', $posts, ['ID' => $post_id]);
        } else {
            $wpdb->insert($wpdb->prefix.'posts', $posts);
            $post_id = $wpdb->insert_id;
        }

        if($post_id) {
            foreach($database_object->get_value('postmeta') as $meta) {
                if(isset($meta['meta_value'])) {
                    $meta['post_id'] = $post_id;
                    if($wpdb->query(
                        'SELECT meta_id FROM '.$wpdb->prefix.'postmeta WHERE post_id = '.$post_id.' AND meta_key = "'.$meta['meta_key'].'";'
                    )) {
                        $wpdb->update($wpdb->prefix.'postmeta', $meta, [
                            'post_id' => $post_id,
                            'meta_key' => $meta['meta_key']
                        ]);
                    } else $wpdb->insert($wpdb->prefix.'postmeta', $meta);
                }
            }

            if(count($database_object->get_value('term_relationships')) > 0) {
                $wpdb->delete($wpdb->prefix.'term_relationships', ['object_id' => $post_id]);
                foreach($database_object->get_value('term_relationships') as $term) {
                    $wpdb->insert($wpdb->prefix.'term_relationships', $term);
                }
            }
        }

        $unique_slug = wp_unique_post_slug(
            $posts['post_name'],
            $post_id,
            $posts['post_status'],
            $posts['post_type'],
            null
        );
        if($unique_slug !== $posts['post_name']) {
            $wpdb->update($wpdb->prefix.'posts', ['post_name' => $unique_slug], ['ID' => $post_id]);
        }

        // Update WooCommerce product lookup table so prices, stock, and
        // other meta are visible on the frontend (raw SQL writes bypass WC hooks).
        if ($post_id && function_exists('tigon_dms_refresh_wc_product_data')) {
            tigon_dms_refresh_wc_product_data($post_id);
        }

        // Purge the WP Rocket cache so visitors see fresh inventory. New
        // products also purge the homepage feed. Deferred automatically
        // during a bulk resync (see Cache::begin_bulk()).
        if ($post_id) {
            \Tigon\DmsConnect\Includes\Cache::purge_product((int) $post_id, empty($posts['ID']));
        }

        return $post_id;
    }

    public static function create_from_database_object(Database_Object $database_object) {
        if ( ! empty( $database_object->get_value('posts', 'ID') ) ) {
            return new \WP_Error(
                "tigon_dms_rest_{$database_object->get_value('posts', 'post_type')}_exists",
                sprintf( __( 'Cannot create existing %s.', 'tigon-dms-connect' ), $database_object->get_value('posts', 'post_type') ),
                array( 'status' => 400 )
            );
        }
        $result = self::write_database_object($database_object);

        if($result) {
            // Mark product as featured and visible
            wp_set_object_terms($result, array('featured'), 'product_visibility');
            return ['pid' => $result, 'onWebsite' => true, 'websiteUrl' => get_permalink($result)];
        }
        return new \WP_Error(
            "tigon_dms_rest_{$database_object->get_value('posts', 'post_type')}_db_write_failure",
            sprintf( __( 'failed to write %s to database.', 'tigon-dms-connect' ), $database_object->get_value('posts', 'post_type') ),
            array( 'status' => 400 )
        );
    }

    public static function update_from_database_object(Database_Object $database_object) {
        if ( ! $database_object->get_value('posts') || 0 == $database_object->get_value('posts', 'ID') ) {
			return new \WP_Error(
                "woocommerce_rest_{$database_object->get_value('posts', 'post_type')}_invalid_id",
                __( 'Invalid ID.', 'woocommerce' ),
                array( 'status' => 400 ) );
		}
        $result = self::write_database_object($database_object);

        if($result) {
            // Mark product as featured and visible
            wp_set_object_terms($result, array('featured'), 'product_visibility');
            return ['pid' => $result, 'onWebsite' => true, 'websiteUrl' => get_permalink($result)];
        }
        return new \WP_Error(
            "tigon_dms_rest_{$database_object->get_value('posts', 'post_type')}_db_write_failure",
            sprintf( __( 'failed to write %s to database.', 'tigon-dms-connect' ), $database_object->get_value('posts', 'post_type') ),
            array( 'status' => 400 )
        );
    }

    public static function delete_by_id($request) {
        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

		$id    = (int) $request->get_value('posts','ID');
		$post  = get_post( $id );

        if ( empty( $id ) || empty( $post->ID )) {
			return new \WP_Error(
                "tigon_dms_rest_invalid_id",
                __( 'ID is invalid.', 'tigon-dms-connect' ),
                array( 'status' => 404 )
            );
		}
        
        // Purge the WP Rocket cache while the post still exists, so its
        // permalink and archive URLs can still be resolved.
        \Tigon\DmsConnect\Includes\Cache::purge_product($id, true);

        $posts = $wpdb->delete($wpdb->prefix.'posts', ['ID' => $id]);
        $postmeta = $wpdb->delete($wpdb->prefix.'postmeta', ['post_id' => $id]);
        $terms = $wpdb->delete($wpdb->prefix.'term_relationships', ['object_id' => $id]);
        $wcds = $wpdb->delete($wpdb->prefix.'wc_product_meta_lookup', ['product_id' => $id]);

        return ['post' => $id, 'onWebsite' => false];
    }
}