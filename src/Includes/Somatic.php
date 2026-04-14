<?php

namespace Tigon\DmsConnect\Includes;

class Somatic {

    private function __construct() {}

/**
     * Download an image from the specified URL and attach it to a post.
     * Modified version of core function media_sideload_image() in /wp-admin/includes/media.php  (which returns an html img tag instead of attachment ID)
     * Additional functionality: ability override actual filename, and to pass $post_data to override values in wp_insert_attachment (original only allowed $desc)
     *
     * @since 1.4 Somatic Framework
     *
     * @param string $url (required) The URL of the image to download
     * @param int $post_id (optional) The post ID the media is to be associated with
     * @param bool $thumb (optional) Whether to make this attachment the Featured Image for the post (post_thumbnail)
     * @param string $filename (optional) Replacement filename for the URL filename (do not include extension)
     * @param array $post_data (optional) Array of key => values for wp_posts table (ex: 'post_title' => 'foobar', 'post_status' => 'draft')
     * @param string $return (optional) Set return type (url, id)
     * @return int|object The ID of the attachment or a \WP_Error on failure
     */
    public static function attach_external_image($url = null, $post_id = null, $thumb = null, $filename = null, $post_data = array(), $metadata = array(), $return = 'id')
    {
        if (!$url)
            return new \WP_Error('missing', "Need a valid URL...");
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        // Download file to temp location, returns full server path to temp file, ex; /home/user/public_html/mysite/wp-content/26192277_640.tmp
        $tmp = download_url($url);

        // If error storing temporarily, unlink
        if (is_wp_error($tmp)) {
            @unlink($file_array['tmp_name']);   // clean up
            $file_array['tmp_name'] = '';
            return $tmp; // output wp_error
        }

        // Extract filename + extension from the URL (stop at query string if any).
        // Supports common image formats + PDF (for Monroney stickers). Case-insensitive.
        // Adding avif / webp / tif(f) / bmp for forward compatibility with modern
        // DMS image pipelines that may emit these formats.
        if (!preg_match('/[^\?]+\.(jpg|jpe|jpeg|gif|png|webp|avif|tif|tiff|bmp|pdf)/i', $url, $matches)) {
            @unlink($tmp);
            return new \WP_Error(
                'unsupported_extension',
                'Could not extract a supported file extension from URL: ' . $url . '. Expected one of: jpg, jpeg, gif, png, webp, avif, tif, bmp, pdf.'
            );
        }
        $url_filename = basename($matches[0]);                                                  // extract filename from url for title
        $url_type = wp_check_filetype($url_filename);                                           // determine file type (ext and mime/type)

        // override filename if given, reconstruct server path
        if (!empty($filename)) {
            $filename = sanitize_file_name($filename);
            $tmppath = pathinfo($tmp);                                                        // extract path parts
            $new = $tmppath['dirname'] . "/" . $filename . "." . $tmppath['extension'];          // build new path
            rename($tmp, $new);                                                                 // renames temp file on server
            $tmp = $new;                                                                        // push new filename (in path) to be used in file array later
        }

        // assemble file data (should be built like $_FILES since wp_handle_sideload() will be using)
        $file_array['tmp_name'] = $tmp;                                                         // full server path to temp file

        if (!empty($filename)) {
            $file_array['name'] = $filename . "." . $url_type['ext'];                           // user given filename for title, add original URL extension
        } else {
            $file_array['name'] = $url_filename;                                                // just use original URL filename
        }

        // set additional wp_posts columns
        if (empty($post_data['post_title'])) {
            $post_data['post_title'] = basename($url_filename, "." . $url_type['ext']);         // just use the original filename (no extension)
        }

        // make sure gets tied to parent
        if (isset($post_id) && empty($post_data['post_parent'])) {
            $post_data['post_parent'] = $post_id;
        }

        // required libraries for media_handle_sideload
        require_once(ABSPATH . 'wp-admin/includes/file.php');
        require_once(ABSPATH . 'wp-admin/includes/media.php');
        require_once(ABSPATH . 'wp-admin/includes/image.php');

        // do the validation and storage stuff
        $att_id = media_handle_sideload($file_array, $post_id, null, $post_data);             // $post_data can override the items saved to wp_posts table, like post_mime_type, guid, post_parent, post_title, post_content, post_status

        // If error storing permanently, unlink
        if (is_wp_error($att_id)) {
            @unlink($file_array['tmp_name']);   // clean up
            return $att_id; // output wp_error
        }

        // set as post thumbnail if desired
        if ($thumb) {
            set_post_thumbnail($post_id, $att_id);
        }

        // set metadata
        foreach($metadata as $meta_key => $meta_value) {
            update_post_meta($att_id, $meta_key, $meta_value);
        }

        return ($return == 'url') ? wp_get_attachment_url($att_id) : $att_id;
    }
}