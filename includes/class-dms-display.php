<?php
/**
 * DMS Display Handler
 *
 * @package DMS_Bridge
 */

if (!defined('ABSPATH')) {
    exit;
}

class DMS_Display {

    // All custom routing helpers removed - using external URLs directly from API

    /**
     * Display featured carts on frontend
     */
    public static function render_carts($type = 'all') {

        // Load stores data once at the beginning (cached in static variable in DMS_API)
        // This ensures we only call the stores API once per page load
        DMS_API::get_stores();

        // Default homepage key
        $key = 'national';

        // IMPORTANT: Check if NOT homepage first
        if (!is_front_page()) {
            // Detect location page via slug
            $queried = get_queried_object();
            if (!empty($queried->post_name)) {
                // Convert hyphens to underscores for API
                // ocean-view → ocean_view
                $slug = str_replace('-', '_', $queried->post_name);
                $key = 'tigon_' . strtolower($slug);
            }
        }

        $response_data = DMS_API::get_featured_carts($key);

        if (empty($response_data['data']) || !is_array($response_data['data'])) {
            return;
        }

        $new_carts_data     = array();
        $used_carts_data    = array();
        $popular_carts_data = array();

        foreach ($response_data['data'] as $section) {
            if ($section['key'] === 'featuredNewCarts') {
                $new_carts_data = $section['carts'] ?? array();
            }
            if ($section['key'] === 'featuredUsedCarts') {
                $used_carts_data = $section['carts'] ?? array();
            }
            if ($section['key'] === 'popularCarts') {
                $popular_carts_data = $section['carts'] ?? array();
            }
        }

        // Get location city and state for location pages (not homepage)
        $location_string = '';
        if ($key !== 'national') {
            // Use the first cart from either new or used carts to get the location
            $first_cart = !empty($new_carts_data) ? $new_carts_data[0] : (!empty($used_carts_data) ? $used_carts_data[0] : null);
            if ($first_cart && isset($first_cart['cartLocation']['locationId'])) {
                $store_id = $first_cart['cartLocation']['locationId'];
                $location_data = DMS_API::get_city_and_state_by_store_id($store_id);
                $city_name = $location_data['city'];
                $state = $location_data['state'];
                
                // Build location string: "City State" (without comma)
                $location_string = trim($city_name . ' ' . $state);
            }
        }

        if ($type === 'all' || $type === 'new') {
            $new_title = $key !== 'national' && !empty($location_string) 
                ? 'NEW GOLF CARTS IN ' . strtoupper($location_string) 
                : 'NEW GOLF CARTS';
            self::render_cart_section($new_carts_data, $new_title);
        }

        if ($type === 'all' || $type === 'used') {
            $used_title = $key !== 'national' && !empty($location_string) 
                ? 'USED GOLF CARTS IN ' . strtoupper($location_string) 
                : 'USED GOLF CARTS';
            self::render_cart_section($used_carts_data, $used_title);
        }

        // Popular carts: only show on homepage (key = 'national')
        if ($key === 'national' && ($type === 'all' || $type === 'popular')) {
            self::render_cart_section($popular_carts_data, 'POPULAR CARTS');
        }
    }

    /**
     * Render a cart section (AUTO-FILL LOGIC)
     */
    private static function render_cart_section($carts_data, $title) {

        // Filter in-stock carts
        $in_stock_carts = array_values(array_filter($carts_data, function ($cart) {
            return isset($cart['isInStock']) && $cart['isInStock'] === true;
        }));

        // Always show the section heading
        echo '<section class="dms-carts">';
        echo '<h2>' . esc_html($title) . '</h2>';

        // If no carts available, show "No carts found" message
        if (empty($in_stock_carts)) {
            echo '<p class="dms-no-carts">No carts found</p>';
            echo '</section>';
            return;
        }

        // Always show first 8 in-stock carts
        $display_carts = array_slice($in_stock_carts, 0, 8);

        echo '<div class="dms-cart-grid">';

        foreach ($display_carts as $cart) {
            self::render_cart_card($cart);
        }

        echo '</div></section>';
    }

    /**
     * Render a single cart card
     */
    private static function render_cart_card($cart) {

        $make         = $cart['cartType']['make'] ?? '';
        $model        = $cart['cartType']['model'] ?? '';
        $color        = $cart['cartAttributes']['cartColor'] ?? '';
        $store_id     = $cart['cartLocation']['locationId'] ?? '';
        $retail_price = $cart['retailPrice'] ?? 0;
        
        // Get cart ID for lazy WooCommerce product creation
        $cart_id = $cart['_id'] ?? '';
        
        // Get city and state from store ID
        $location_data = DMS_API::get_city_and_state_by_store_id($store_id);
        $city_name = $location_data['city'];
        $state = $location_data['state'];
        
        $location_string = $city_name;
        if (!empty($state)) {
            $location_string = $city_name . ', ' . $state;
        }
        
        // Use /dms/cart/{id} route for lazy WooCommerce product creation
        // This route creates/updates the product and redirects to WooCommerce permalink
        $inventory_url = home_url('/dms/cart/' . $cart_id . '/');
    
        // Build cart title with ® between make and model
        $title_parts = array();
        if (!empty($make) && !empty($model)) {
            $title_parts[] = $make . '® ' . $model;
        } elseif (!empty($make)) {
            $title_parts[] = $make;
        } elseif (!empty($model)) {
            $title_parts[] = $model;
        }
        if (!empty($color)) {
            $title_parts[] = $color;
        }
        $cart_title = trim(implode(' ', $title_parts)) . ' In ' . $location_string;
        $formatted_price = '$' . number_format($retail_price, 2);
    
        $image_url = '';
        if (!empty($cart['imageUrls']) && is_array($cart['imageUrls'])) {
            $image_url = DMS_API::get_s3_carts_url() . $cart['imageUrls'][0];                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
        }
    
        echo '<article class="dms-cart">';
        
        if ($image_url) {
            echo '<img src="' . esc_url($image_url) . '" alt="' . esc_attr($cart_title) . '">';
        } else {
            $placeholder_url = function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('woocommerce_thumbnail') : '';
            if ($placeholder_url) {
                echo '<img src="' . esc_url($placeholder_url) . '" alt="' . esc_attr($cart_title) . '">';
            } else {
                echo '<img src="' . esc_url(DMS_API::get_coming_soon_image()) . '" alt="Coming Soon">';
            }
        }
        
        echo '<h3>' . esc_html($cart_title) . '</h3>';
        echo '<p class="price">' . esc_html($formatted_price) . '</p>';
        
        echo '<a href="' . esc_url($inventory_url) . '" class="dms-see-details-btn">';
        echo '<i class="fa-solid fa-cart-shopping" style="color: #fff;"></i> See Details';
        echo '</a>';
        
        echo '</article>';
    }
    /**
     * Get add-ons for a specific cart make and model
     *
     * @param string $make Cart make
     * @param string $model Cart model
     * @return array Array of add-on posts with their data
     */
    private static function get_addons_for_cart($make, $model) {
        if (empty($make) || empty($model)) {
            return array();
        }

        // Query add-ons by taxonomy terms
        // Try to find terms matching make and model in the "added-features" taxonomy
        // First, try combined format (e.g., "Yamaha-G2")
        $combined_slug = sanitize_title($make . '-' . $model);
        $combined_name = sanitize_text_field($make . ' ' . $model);
        $term_combined = get_term_by('slug', $combined_slug, 'added-features');
        if (!$term_combined) {
            $term_combined = get_term_by('name', $combined_name, 'added-features');
        }
        
        // Build taxonomy query array
        $tax_query = array();
        
        if ($term_combined) {
            // Found combined term (e.g., "Yamaha-G2")
            $tax_query[] = array(
                'taxonomy' => 'added-features',
                'field'    => 'term_id',
                'terms'    => $term_combined->term_id,
            );
        } else {
            // Try separate make and model terms
            $term_make = get_term_by('name', sanitize_text_field($make), 'added-features');
            $term_model = get_term_by('name', sanitize_text_field($model), 'added-features');
            
            if ($term_make && $term_model) {
                // Query posts that have BOTH make and model terms
                $tax_query[] = array(
                    'taxonomy' => 'added-features',
                    'field'    => 'term_id',
                    'terms'    => array($term_make->term_id, $term_model->term_id),
                    'operator' => 'AND',
                );
            } elseif ($term_make) {
                // Only make term found
                $tax_query[] = array(
                    'taxonomy' => 'added-features',
                    'field'    => 'term_id',
                    'terms'    => $term_make->term_id,
                );
            } elseif ($term_model) {
                // Only model term found
                $tax_query[] = array(
                    'taxonomy' => 'added-features',
                    'field'    => 'term_id',
                    'terms'    => $term_model->term_id,
                );
            }
        }

        // Query add-ons that match the cart's make and model via taxonomy
        $args = array(
            'post_type'      => 'addon', // Adjust if your CPT slug is different
            'post_status'    => 'publish',
            'posts_per_page' => -1, // Get all matching add-ons
        );
        
        // Only add tax_query if we found matching terms
        if (!empty($tax_query)) {
            $args['tax_query'] = $tax_query;
        } else {
            // No matching terms found, return empty array
            return array();
        }

        $addon_posts = get_posts($args);
        $addons = array();

        foreach ($addon_posts as $post) {
            $price = get_post_meta($post->ID, 'addon_price', true);
            $image_id = get_post_thumbnail_id($post->ID);
            $image_url = '';
            if ($image_id) {
                $image_url = wp_get_attachment_image_url($image_id, 'medium');
            }

            $addons[] = array(
                'id'       => $post->ID,
                'title'    => $post->post_title,
                'price'    => floatval($price),
                'image'    => $image_url,
                'slug'     => $post->post_name
            );
        }

        return $addons;
    }

    /**
     * Render single cart detail page
     *
     * @param string $cart_id Cart ID (_id)
     */
    public static function render_single_cart($cart_id) {
        $cart = DMS_API::get_cart($cart_id);

        if (empty($cart) || !is_array($cart)) {
            status_header(404);
            echo '<div class="dms-cart-not-found"><h1>Cart Not Found</h1><p>The requested cart could not be found.</p></div>';
            return;
        }

        // Check if sold (isInStock === false)
        $is_in_stock = isset($cart['isInStock']) && $cart['isInStock'] === true;

        // Extract cart data
        $make = $cart['cartType']['make'] ?? '';
        $model = $cart['cartType']['model'] ?? '';
        $year = $cart['cartType']['year'] ?? '';
        $color = $cart['cartAttributes']['cartColor'] ?? '';
        $retail_price = $cart['retailPrice'] ?? 0;
        $store_id = $cart['cartLocation']['locationId'] ?? '';
        $image_urls = $cart['imageUrls'] ?? array();
        $website_images = $cart['advertising']['websiteUrl'] ?? '';
        
        // Only use website images from imageUrls array
        $gallery_images = array();
        if (!empty($image_urls) && is_array($image_urls)) {
            $s3_carts_url = DMS_API::get_s3_carts_url();
            foreach ($image_urls as $img) {
                $gallery_images[] = $s3_carts_url . $img;                                                                                                                                                                                                                                                                                                                                                                                                                                                                       
            }
        }

        // Get location data
        $location_data = DMS_API::get_city_and_state_by_store_id($store_id);
        $city_name = $location_data['city'];
        $state = $location_data['state'];
        $location_string = trim($city_name . ' ' . $state);

        // Format price
        $formatted_price = '$' . number_format($retail_price, 2);

        // Monroney sticker data (if available)
        $monroney_data = $cart['monroney'] ?? array();

        // Cart attributes
        $cart_attrs = $cart['cartAttributes'] ?? array();
        $seat_color = $cart_attrs['seatColor'] ?? '';
        $drive_train = $cart_attrs['driveTrain'] ?? '';
        $passengers = $cart_attrs['passengers'] ?? '';

        // Display single cart page
        ?>
        <div class="dms-single-cart">
            <?php if (!$is_in_stock): ?>
                <div class="dms-cart-sold-banner">
                    <h2>This cart has been sold</h2>
                </div>
            <?php endif; ?>

            <!-- Image Gallery -->
            <div class="dms-cart-gallery">
                <?php if (!empty($gallery_images)): ?>
                    <div class="dms-gallery-main">
                        <img src="<?php echo esc_url($gallery_images[0]); ?>" alt="<?php echo esc_attr($make . ' ' . $model); ?>" id="dms-main-image">
                    </div>
                    <?php if (count($gallery_images) > 1): ?>
                        <div class="dms-gallery-thumbnails">
                            <?php foreach ($gallery_images as $index => $img_url): ?>
                                <img src="<?php echo esc_url($img_url); ?>" alt="Thumbnail <?php echo $index + 1; ?>" class="dms-gallery-thumb <?php echo $index === 0 ? 'active' : ''; ?>" onclick="document.getElementById('dms-main-image').src = '<?php echo esc_js($img_url); ?>'; document.querySelectorAll('.dms-gallery-thumb').forEach(t => t.classList.remove('active')); this.classList.add('active');">
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="dms-gallery-main">
                        <?php
                        $placeholder_url = function_exists('wc_placeholder_img_src') ? wc_placeholder_img_src('woocommerce_single') : '';
                        if ($placeholder_url): ?>
                            <img src="<?php echo esc_url($placeholder_url); ?>" alt="<?php echo esc_attr($make . ' ' . $model); ?>" id="dms-main-image">
                        <?php else: ?>
                            <div class="dms-cart-no-image-large">
                                <span>No Image Available</span>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Location Banner -->
                <div class="dms-location-banner">
                    <span><?php echo esc_html($location_string); ?></span>
                </div>
            </div>

            <!-- Cart Information -->
            <div class="dms-cart-info">
                <h1 class="dms-cart-title"><?php echo esc_html($make . ' ' . $model . ' ' . $year); ?></h1>
                <p class="dms-cart-color">Color: <?php echo esc_html($color); ?></p>
                <?php if (!empty($seat_color)): ?>
                    <p class="dms-cart-seat-color">Seat Color: <?php echo esc_html($seat_color); ?></p>
                <?php endif; ?>
                <?php if (!empty($drive_train)): ?>
                    <p class="dms-cart-drivetrain">Drive Train: <?php echo esc_html($drive_train); ?></p>
                <?php endif; ?>
                <?php if (!empty($passengers)): ?>
                    <p class="dms-cart-passengers"><?php echo esc_html($passengers); ?></p>
                <?php endif; ?>
                <p class="dms-cart-price"><?php echo esc_html($formatted_price); ?></p>
                
                <?php if ($is_in_stock): ?>
                    <a href="#contact" class="dms-contact-btn">Contact Us</a>
                <?php else: ?>
                    <button class="dms-sold-btn" disabled>Sold</button>
                <?php endif; ?>
            </div>

            <!-- Add-ons Section -->
            <div class="dms-cart-addons">
                <h2>Available Add-ons</h2>
                <div class="dms-addons-grid" id="dms-addons-grid">
                    <?php
                    $addons = self::get_addons_for_cart($make, $model);
                    if (!empty($addons)):
                        foreach ($addons as $addon):
                            $addon_price_formatted = '$' . number_format($addon['price'], 2);
                    ?>
                    <div class="dms-addon-item">
                        <?php if (!empty($addon['image'])): ?>
                            <img src="<?php echo esc_url($addon['image']); ?>" alt="<?php echo esc_attr($addon['title']); ?>">
                        <?php endif; ?>
                        <h3><?php echo esc_html($addon['title']); ?></h3>
                        <p class="dms-addon-price"><?php echo esc_html($addon_price_formatted); ?></p>
                        <label>
                            <input type="checkbox" 
                                   class="dms-addon-checkbox" 
                                   data-price="<?php echo esc_attr($addon['price']); ?>"
                                   data-addon-id="<?php echo esc_attr($addon['id']); ?>"
                                   value="<?php echo esc_attr($addon['id']); ?>">
                            Select Add-on
                        </label>
                    </div>
                    <?php
                        endforeach;
                    else:
                    ?>
                        <p class="dms-no-addons">No add-ons available at this time.</p>
                    <?php endif; ?>
                </div>
                <?php if (!empty($addons)): ?>
                    <div class="dms-total-price" style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #e0e0e0;">
                        <strong>Cart Total: <span id="dms-cart-total"><?php echo esc_html($formatted_price); ?></span></strong>
                    </div>
                <?php endif; ?>
            </div>

            <?php if (!empty($addons)): ?>
            <script>
            (function() {
                var basePrice = <?php echo floatval($retail_price); ?>;
                var totalElement = document.getElementById('dms-cart-total');
                var checkboxes = document.querySelectorAll('.dms-addon-checkbox');
                
                function updateTotal() {
                    var total = basePrice;
                    checkboxes.forEach(function(checkbox) {
                        if (checkbox.checked) {
                            total += parseFloat(checkbox.getAttribute('data-price')) || 0;
                        }
                    });
                    if (totalElement) {
                        totalElement.textContent = '$' + total.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                    }
                }
                
                checkboxes.forEach(function(checkbox) {
                    checkbox.addEventListener('change', updateTotal);
                });
            })();
            </script>
            <?php endif; ?>

            <!-- Monroney Sticker -->
            <?php if (!empty($monroney_data)): ?>
                <div class="dms-monroney-sticker">
                    <h2>Specifications</h2>
                    <div class="dms-monroney-content">
                        <?php
                        // Display Monroney data
                        foreach ($monroney_data as $key => $value) {
                            if (!empty($value)) {
                                echo '<p><strong>' . esc_html(ucwords(str_replace('_', ' ', $key))) . ':</strong> ' . esc_html($value) . '</p>';
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Extended Warranty Banner -->
            <div class="dms-warranty-banner">
                <h3>Extended Warranty Available</h3>
                <p>Protect your investment with our comprehensive extended warranty options.</p>
                <a href="#warranty" class="dms-warranty-btn">Learn More</a>
            </div>

            <!-- SEO Description (hidden visually, but in DOM) -->
            <div class="dms-seo-description" style="position: absolute; left: -9999px; width: 1px; height: 1px; overflow: hidden;">
                <p>
                    <?php
                    $seo_parts = array();
                    if (!empty($make)) $seo_parts[] = $make;
                    if (!empty($model)) $seo_parts[] = $model;
                    if (!empty($year)) $seo_parts[] = $year;
                    if (!empty($color)) $seo_parts[] = $color;
                    
                    $seo_text = implode(' ', $seo_parts);
                    if (!empty($location_string)) {
                        $seo_text .= ' in ' . $location_string;
                    }
                    if (!empty($retail_price)) {
                        $seo_text .= '. Price: ' . $formatted_price;
                    }
                    if (!empty($cart['notes'])) {
                        $seo_text .= '. ' . $cart['notes'];
                    }
                    
                    echo esc_html($seo_text);
                    ?>
                </p>
            </div>
        </div>
        <?php
    }
}
