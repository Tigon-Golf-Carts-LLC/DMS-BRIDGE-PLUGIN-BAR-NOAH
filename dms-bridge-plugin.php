<?php
/**
 * Plugin Name: DMS Bridge Plugin
 * Description: Tigon DMS Connect — fetches, imports, maps and displays golf carts from the DMS into WooCommerce.
 * Version: 2.0.0
 * Author: Tigon Golf Carts
 * Author URI: https://tigongolfcarts.com/
 * Text Domain: tigon-dms-connect
 * Requires Plugins: woocommerce
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Plugin constants
 */
define('TIGON_DMS_VERSION', '2.0.0');
define('TIGON_DMS_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TIGON_DMS_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Configuration: Set to false to show carts WITH default homepage content
 * Set to true to replace homepage entirely with carts (no default content)
 */
if (!defined('TIGON_DMS_HIDE_DEFAULT_CONTENT')) {
    define('TIGON_DMS_HIDE_DEFAULT_CONTENT', false); // Default: show both
}

/**
 * Load required files — DMS Bridge (original)
 */
require_once TIGON_DMS_PLUGIN_DIR . 'includes/class-dms-api.php';
require_once TIGON_DMS_PLUGIN_DIR . 'includes/class-dms-display.php';
require_once TIGON_DMS_PLUGIN_DIR . 'includes/class-dms-sync.php';

/**
 * ============================================================================
 * DMS CONNECT — Full DMS import/mapping engine
 * ============================================================================
 *
 * DMS Connect provides:
 * - Admin UI (diagnostics, import wizard, settings)
 * - REST API endpoints for DMS push (used/new carts, product grids)
 * - Rich field mapping (50+ WooCommerce fields, 10+ taxonomies, 30+ attributes)
 * - Direct database writes for high-performance imports
 * - New cart template system (40+ pre-configured golf cart models)
 * - Featured product grid management (Elementor)
 * - Product archive extensions (custom ordering, price sort)
 * - GitHub auto-updater
 */
require_once TIGON_DMS_PLUGIN_DIR . 'vendor/autoload.php';
\Tigon\DmsConnect\Core::init();

/**
 * ============================================================================
 * LAZY WOOCOMMERCE PRODUCT CREATION
 * Route: /dms/cart/{id} → Creates/updates WooCommerce product → Redirects
 * ============================================================================
 */

/**
 * Add rewrite rule for /dms/cart/{id}
 */
function tigon_dms_add_cart_route() {
    add_rewrite_rule(
        '^dms/cart/([a-zA-Z0-9]+)/?$',
        'index.php?dms_cart_id=$matches[1]',
        'top'
    );
}
add_action('init', 'tigon_dms_add_cart_route');

/**
 * Register query var for cart ID
 */
function tigon_dms_add_query_vars($vars) {
    $vars[] = 'dms_cart_id';
    return $vars;
}
add_filter('query_vars', 'tigon_dms_add_query_vars');

/**
 * Handle /dms/cart/{id} route - create/update product and redirect
 * No UI rendered - redirect only
 */
function tigon_dms_handle_cart_route() {
    $cart_id = get_query_var('dms_cart_id');
    
    if (empty($cart_id)) {
        return;
    }

    // Fetch cart data from DMS API
    $cart_data = DMS_API::get_cart($cart_id);
    
    if (empty($cart_data)) {
        // Cart not found - redirect to shop or 404
        wp_redirect(home_url('/shop/'));
        exit;
    }
    
    // Create or update WooCommerce product
    $product_id = tigon_dms_ensure_woo_product($cart_data, $cart_id);
    
    if (!$product_id) {
        // Failed to create product - redirect to shop
        wp_redirect(home_url('/shop/'));
        exit;
    }
    
    // Redirect to the WooCommerce product page
    $product_url = get_permalink($product_id);
    wp_redirect($product_url, 302);
    exit;
}
add_action('template_redirect', 'tigon_dms_handle_cart_route', 5);

/**
 * Normalize DMS cart title for consistency
 * 
 * Ensures all titles use " In " format:
 * "Denago Nomad Blue – Ocean View, NJ" → "Denago Nomad Blue In Ocean View, NJ"
 * "Denago Nomad Blue - Ocean View, NJ" → "Denago Nomad Blue In Ocean View, NJ"
 *
 * @param string $title Raw DMS cart title
 * @return string Normalized title with " In "
 */
function tigon_dms_normalize_title($title) {
    // Convert en-dash (–) to " In "
    $normalized = str_replace(' – ', ' In ', $title);
    
    // Convert regular hyphen (-) to " In "
    $normalized = str_replace(' - ', ' In ', $normalized);
    
    // Normalize whitespace (collapse multiple spaces)
    $normalized = preg_replace('/\s+/', ' ', $normalized);
    
    return trim($normalized);
}

/**
 * Parse DMS cart data into structured specs for product meta
 * 
 * Maps the actual /get-cart-by-id API response to Feature/Description pairs
 * for the WooCommerce Description tab.
 *
 * @param array $cart_data Full DMS cart payload from API
 * @return array Parsed specs array with Feature => Description pairs
 */
function tigon_dms_parse_cart_specs($cart_data) {
    $specs = array();
    
    // ========== DESCRIPTION TAB FIELDS ==========
    
    // Cart Type: Make, Model, Year
    if (!empty($cart_data['cartType'])) {
        $cart_type = $cart_data['cartType'];
        if (!empty($cart_type['make'])) {
            $specs['Make'] = $cart_type['make'];
        }
        if (!empty($cart_type['model'])) {
            $specs['Model'] = $cart_type['model'];
        }
        if (!empty($cart_type['year'])) {
            $specs['Year'] = $cart_type['year'];
        }
    }
    
    // Street Legal (from title.isStreetLegal)
    if (isset($cart_data['title']['isStreetLegal'])) {
        $specs['Street Legal'] = $cart_data['title']['isStreetLegal'] ? 'Fully Street Legal' : 'No';
    }
    
    // Cart Attributes
    if (!empty($cart_data['cartAttributes'])) {
        $attrs = $cart_data['cartAttributes'];
        
        // Color
        if (!empty($attrs['cartColor'])) {
            $specs['Color'] = $attrs['cartColor'];
        }
        
        // Seat Color
        if (!empty($attrs['seatColor'])) {
            $specs['Seat Color'] = $attrs['seatColor'];
        }
        
        // Tires (tireType field)
        if (!empty($attrs['tireType'])) {
            $specs['Tires'] = $attrs['tireType'];
        }
        
        // Rims (tireRimSize field)
        if (!empty($attrs['tireRimSize'])) {
            $specs['Rims'] = $attrs['tireRimSize'] . '"';
        }
        
        // Drivetrain
        if (!empty($attrs['driveTrain'])) {
            $specs['Drivetrain'] = $attrs['driveTrain'];
        }
        
        // Passengers
        if (!empty($attrs['passengers'])) {
            $specs['Passengers'] = $attrs['passengers'];
        }
        
        // Sound System (hasSoundSystem)
        if (isset($attrs['hasSoundSystem']) && $attrs['hasSoundSystem'] !== null) {
            $specs['Sound System'] = $attrs['hasSoundSystem'] ? 'Yes' : 'No';
        }
        
        // Lift Kit (isLifted)
        if (isset($attrs['isLifted']) && $attrs['isLifted'] !== null) {
            $specs['Lift Kit'] = $attrs['isLifted'] ? 'Yes' : 'No';
        }
        
        // Receiver Hitch (hasHitch)
        if (isset($attrs['hasHitch']) && $attrs['hasHitch'] !== null) {
            $specs['Receiver Hitch'] = $attrs['hasHitch'] ? 'Yes' : 'No';
        }
        
        // Extended Top (hasExtendedTop)
        if (isset($attrs['hasExtendedTop']) && $attrs['hasExtendedTop'] !== null) {
            $specs['Extended Top'] = $attrs['hasExtendedTop'] ? 'Yes' : 'No';
        }
    }
    
    // Battery Information
    if (!empty($cart_data['battery']) && is_array($cart_data['battery'])) {
        $battery = $cart_data['battery'];
        
        if (!empty($battery['type'])) {
            $specs['Battery Type'] = $battery['type'];
        }
        if (!empty($battery['brand'])) {
            $specs['Battery Brand'] = $battery['brand'];
        }
        if (!empty($battery['year'])) {
            $specs['Battery Year'] = $battery['year'];
        }
        if (!empty($battery['ampHours'])) {
            $specs['Capacity'] = $battery['ampHours'] . ' Amp Hours';
        }
        if (!empty($battery['batteryVoltage'])) {
            $specs['Battery Voltage'] = $battery['batteryVoltage'] . 'V';
        }
        if (!empty($battery['warrantyLength'])) {
            $specs['Battery Warranty'] = $battery['warrantyLength'];
        }
    }
    
    // Engine Information (for gas carts)
    if (!empty($cart_data['engine']) && is_array($cart_data['engine'])) {
        $engine = $cart_data['engine'];
        
        if (!empty($engine['make'])) {
            $specs['Engine'] = $engine['make'];
        }
        if (!empty($engine['horsepower'])) {
            $specs['Horsepower'] = $engine['horsepower'] . ' HP';
        }
        if (!empty($engine['stroke'])) {
            $specs['Stroke'] = $engine['stroke'];
        }
    }
    
    // Vehicle Warranty (top-level warrantyLength)
    if (!empty($cart_data['warrantyLength'])) {
        $specs['Warranty'] = $cart_data['warrantyLength'];
    }
    
    // ========== ADDITIONAL INFORMATION FIELDS ==========
    
    // Vehicle Power (isElectric)
    if (isset($cart_data['isElectric'])) {
        $specs['Vehicle Power'] = $cart_data['isElectric'] ? 'ELECTRIC' : 'GAS';
    }
    
    // Vehicle Status (isUsed)
    if (isset($cart_data['isUsed'])) {
        $specs['Vehicle Status'] = $cart_data['isUsed'] ? 'USED' : 'NEW';
    }
    
    // Serial Number
    if (!empty($cart_data['serialNo'])) {
        $specs['Serial Number'] = $cart_data['serialNo'];
    }
    
    // VIN Number
    if (!empty($cart_data['vinNo'])) {
        $specs['VIN'] = $cart_data['vinNo'];
    }
    
    // Odometer
    if (!empty($cart_data['odometer'])) {
        $specs['Odometer'] = $cart_data['odometer'];
    }
    
    // Hours
    if (!empty($cart_data['hour'])) {
        $specs['Hours'] = $cart_data['hour'];
    }
    
    // Vehicle Location
    if (!empty($cart_data['cartLocation'])) {
        $store_id = $cart_data['cartLocation']['locationId'] ?? '';
        if ($store_id && class_exists('DMS_API')) {
            $location_data = DMS_API::get_city_and_state_by_store_id($store_id);
            if (!empty($location_data['city']) || !empty($location_data['state'])) {
                $location_parts = array();
                if (!empty($location_data['city'])) {
                    $location_parts[] = $location_data['city'];
                }
                if (!empty($location_data['state'])) {
                    $location_parts[] = $location_data['state'];
                }
                if (!empty($location_parts)) {
                    $specs['Vehicle Location'] = implode(', ', $location_parts);
                }
            }
        }
    }
    
    // Year of Vehicle (duplicate for Additional Info format)
    if (!empty($cart_data['cartType']['year'])) {
        $specs['Year of Vehicle'] = $cart_data['cartType']['year'];
    }
    
    return $specs;
}

/**
 * Extract image URLs from DMS cart data
 *
 * @param array $cart_data Full DMS cart payload
 * @return array Array of image filenames/URLs
 */
function tigon_dms_parse_cart_images($cart_data) {
    return $cart_data['imageUrls'] ?? array();
}

/**
 * Extract warranty information from DMS cart data
 *
 * @param array $cart_data Full DMS cart payload
 * @return array Warranty data array
 */
function tigon_dms_parse_cart_warranty($cart_data) {
    $warranty = array();

    // Extract any warranty-related fields from cart data
    // Adjust these keys based on actual DMS payload structure
    if (!empty($cart_data['warranty'])) {
        $warranty = $cart_data['warranty'];
    }

    return $warranty;
}

/**
 * Convert a state name to its 2-letter USPS abbreviation.
 * Accepts an already-abbreviated 2-letter code as pass-through.
 *
 * @param string $state State name or 2-letter code
 * @return string 2-letter uppercase code, or original string if not recognized
 */
function tigon_dms_get_state_abbr($state) {
    $state = trim((string) $state);
    if ($state === '') {
        return '';
    }
    if (strlen($state) === 2) {
        return strtoupper($state);
    }
    $map = array(
        'Alabama' => 'AL', 'Alaska' => 'AK', 'Arizona' => 'AZ', 'Arkansas' => 'AR',
        'California' => 'CA', 'Colorado' => 'CO', 'Connecticut' => 'CT', 'Delaware' => 'DE',
        'Florida' => 'FL', 'Georgia' => 'GA', 'Hawaii' => 'HI', 'Idaho' => 'ID',
        'Illinois' => 'IL', 'Indiana' => 'IN', 'Iowa' => 'IA', 'Kansas' => 'KS',
        'Kentucky' => 'KY', 'Louisiana' => 'LA', 'Maine' => 'ME', 'Maryland' => 'MD',
        'Massachusetts' => 'MA', 'Michigan' => 'MI', 'Minnesota' => 'MN', 'Mississippi' => 'MS',
        'Missouri' => 'MO', 'Montana' => 'MT', 'Nebraska' => 'NE', 'Nevada' => 'NV',
        'New Hampshire' => 'NH', 'New Jersey' => 'NJ', 'New Mexico' => 'NM', 'New York' => 'NY',
        'North Carolina' => 'NC', 'North Dakota' => 'ND', 'Ohio' => 'OH', 'Oklahoma' => 'OK',
        'Oregon' => 'OR', 'Pennsylvania' => 'PA', 'Rhode Island' => 'RI', 'South Carolina' => 'SC',
        'South Dakota' => 'SD', 'Tennessee' => 'TN', 'Texas' => 'TX', 'Utah' => 'UT',
        'Vermont' => 'VT', 'Virginia' => 'VA', 'Washington' => 'WA', 'West Virginia' => 'WV',
        'Wisconsin' => 'WI', 'Wyoming' => 'WY', 'District of Columbia' => 'DC',
    );
    $key = ucwords(strtolower($state));
    return $map[$key] ?? $state;
}

/**
 * Compute the location watermark text (_tigonwm) for a DMS cart.
 * Matches the legacy mapped-logic format: "{City Short} {ST}" or "TIGON® RENTALS" for rentals.
 *
 * @param array $cart_data Full DMS cart payload
 * @return string Watermark text (e.g., "Lecanto FL", "South Bend IN", "TIGON® RENTALS")
 */
function tigon_dms_get_watermark_text($cart_data) {
    if (!empty($cart_data['isRental'])) {
        return 'TIGON® RENTALS';
    }

    $store_id = $cart_data['cartLocation']['locationId'] ?? '';
    if (empty($store_id) || !class_exists('DMS_API')) {
        return 'TIGON®';
    }

    $location_data = DMS_API::get_city_and_state_by_store_id($store_id);
    $city = $location_data['city'] ?? '';
    $state = $location_data['state'] ?? '';

    if ($city === '' && $state === '') {
        return 'TIGON®';
    }

    // Short-name overrides for cities with long/compound names.
    $city_short_map = array(
        'Scranton Wilkes-Barre' => 'Scranton',
    );
    $city_short = $city_short_map[$city] ?? $city;
    $state_abbr = tigon_dms_get_state_abbr($state);

    return trim($city_short . ' ' . $state_abbr);
}

/**
 * Create or update WooCommerce product from DMS cart data
 *
 * @param array  $cart_data Full DMS cart payload
 * @param string $cart_id   DMS cart ID (_id)
 * @return int|false Product ID or false on failure
 */
function tigon_dms_ensure_woo_product($cart_data, $cart_id) {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return false;
    }
    
    // Extract cart details
    $make         = $cart_data['cartType']['make'] ?? '';
    $model        = $cart_data['cartType']['model'] ?? '';
    $color        = $cart_data['cartAttributes']['cartColor'] ?? '';
    $retail_price = $cart_data['retailPrice'] ?? 0;
    $store_id     = $cart_data['cartLocation']['locationId'] ?? '';
    
    // Get location info
    $location_data = DMS_API::get_city_and_state_by_store_id($store_id);
    $city = $location_data['city'];
    $state = $location_data['state'];
    $location_string = trim($city . ', ' . $state, ', ');
    
    // Build product title with ® between make and model (using " In " for location separator)
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
    $title = trim(implode(' ', $title_parts));
    if (!empty($location_string)) {
        $title .= ' In ' . $location_string;
    }
    
    // Parse DMS cart data for meta storage
    $specs = tigon_dms_parse_cart_specs($cart_data);
    $images = tigon_dms_parse_cart_images($cart_data);
    $warranty = tigon_dms_parse_cart_warranty($cart_data);
    
    // Check if product already exists
    $existing_product_id = tigon_dms_get_product_by_cart_id($cart_id);
    
    if ($existing_product_id) {
        // Update existing product
        return tigon_dms_update_woo_product($existing_product_id, $title, $retail_price, $cart_data, $specs, $images, $warranty);
    }
    
    // Create new product
    return tigon_dms_create_woo_product($cart_id, $title, $retail_price, $cart_data, $specs, $images, $warranty);
}

/**
 * Get existing product category (does not create new categories)
 *
 * @param string $category_name Category name
 * @param int    $parent_id     Parent category ID (0 for top-level, or pass to find child)
 * @return int Category term ID, or 0 if not found
 */
function tigon_dms_get_existing_category($category_name, $parent_id = null) {
    if (empty($category_name)) {
        return 0;
    }
    
    $sanitized_name = sanitize_text_field($category_name);
    $slug = sanitize_title($sanitized_name);
    
    // Try to find existing category by slug
    $term = get_term_by('slug', $slug, 'product_cat');
    
    if ($term) {
        // If parent_id is specified, verify it matches
        if ($parent_id !== null && $term->parent != $parent_id) {
            return 0; // Category exists but with different parent
        }
        return (int) $term->term_id;
    }
    
    return 0; // Category doesn't exist
}

/**
 * Find existing WooCommerce product by DMS cart ID
 *
 * @param string $cart_id DMS cart ID
 * @return int|false Product ID or false if not found
 */
function tigon_dms_get_product_by_cart_id($cart_id) {
    global $wpdb;
    
    $product_id = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} 
             WHERE meta_key = '_dms_cart_id' AND meta_value = %s 
             LIMIT 1",
            $cart_id
        )
    );
    
    return $product_id ? (int) $product_id : false;
}

/**
 * Create a new WooCommerce product from DMS cart
 *
 * @param string $cart_id    DMS cart ID
 * @param string $title      Product title
 * @param float  $price      Retail price
 * @param array  $cart_data  Full DMS payload
 * @param array  $specs      Parsed specs array
 * @param array  $images     Image URLs array
 * @param array  $warranty   Warranty data array
 * @return int|false Product ID or false on failure
 */
function tigon_dms_create_woo_product($cart_id, $title, $price, $cart_data, $specs = array(), $images = array(), $warranty = array()) {
    // Normalize title for consistency (e.g., "In" → "-")
    $normalized_title = tigon_dms_normalize_title($title);
    
    // Create product post
    $product_id = wp_insert_post(array(
        'post_title'   => sanitize_text_field($normalized_title),
        'post_name'    => sanitize_title($normalized_title),
        'post_status'  => 'publish',
        'post_type'    => 'product',
        'post_content' => '', // Content comes from DMS payload via template
    ));
    
    if (is_wp_error($product_id) || !$product_id) {
        return false;
    }
    
    // Set product type to simple
    wp_set_object_terms($product_id, 'simple', 'product_type');
    
    // Make product visible in catalog (needed for inventory page)
    // Set visibility to 'visible' (appears in both catalog and search)
    wp_set_object_terms($product_id, array('visible'), 'product_visibility');
    
    // Assign product categories (only use existing categories, don't create new ones)
    $categories = array();
    
    // Extract data from cart
    $make = $cart_data['cartType']['make'] ?? '';
    $model = $cart_data['cartType']['model'] ?? '';
    $is_used = isset($cart_data['isUsed']) && $cart_data['isUsed'] === true;
    $drive_train = $cart_data['cartAttributes']['driveTrain'] ?? '';
    $passengers = $cart_data['cartAttributes']['passengers'] ?? '';
    $store_id = $cart_data['cartLocation']['locationId'] ?? '';
    
    // 1. Make + Model categories (keep existing logic)
    if (!empty($make)) {
        $make_cat_id = tigon_dms_get_existing_category($make, 0);
        if ($make_cat_id) {
            $categories[] = $make_cat_id;
            
            // Make+Model subcategory
            if (!empty($model)) {
                $make_model = trim($make . ' ' . $model);
                $model_cat_id = tigon_dms_get_existing_category($make_model, $make_cat_id);
                if ($model_cat_id) {
                    $categories[] = $model_cat_id;
                }
            }
        }
    }
    
    // 2. Condition category (keep existing logic)
    $condition_parent_id = tigon_dms_get_existing_category('Condition', 0);
    if ($condition_parent_id) {
        $condition = $is_used ? 'Used' : 'New';
        $condition_cat_id = tigon_dms_get_existing_category($condition, $condition_parent_id);
        if ($condition_cat_id) {
            $categories[] = $condition_cat_id;
        }
    }
    
    // 3. DriveTrain category
    if (!empty($drive_train)) {
        $drivetrain_parent_id = tigon_dms_get_existing_category('DriveTrain', 0);
        if ($drivetrain_parent_id) {
            // Normalize driveTrain value (e.g., "2X4" -> "2x4", "4X4" -> "4x4")
            $drivetrain_value = strtolower($drive_train);
            $drivetrain_cat_id = tigon_dms_get_existing_category($drivetrain_value, $drivetrain_parent_id);
            if (!$drivetrain_cat_id) {
                // Try uppercase version
                $drivetrain_cat_id = tigon_dms_get_existing_category(strtoupper($drive_train), $drivetrain_parent_id);
            }
            if ($drivetrain_cat_id) {
                $categories[] = $drivetrain_cat_id;
            }
        }
    }
    
    // 4. Location category (State -> City hierarchy)
    if (!empty($store_id)) {
        $store_data = DMS_API::get_city_and_state_by_store_id($store_id);
        $city = $store_data['city'] ?? '';
        $state = $store_data['state'] ?? '';
        
        if (!empty($state)) {
            $location_parent_id = tigon_dms_get_existing_category('Location', 0);
            if ($location_parent_id) {
                // Find state category (child of Location)
                $state_cat_id = tigon_dms_get_existing_category($state, $location_parent_id);
                if ($state_cat_id && !empty($city)) {
                    // Find city category (child of State)
                    $city_cat_id = tigon_dms_get_existing_category($city, $state_cat_id);
                    if ($city_cat_id) {
                        $categories[] = $city_cat_id;
                    }
                }
            }
        }
    }
    
    // 5. Passengers category
    if (!empty($passengers)) {
        // Normalize passengers value (e.g., "6 Passenger" -> "6 seater")
        $passengers_normalized = strtolower(str_replace(' passenger', ' seater', $passengers));
        $passengers_parent_id = tigon_dms_get_existing_category('Passengers', 0);
        if ($passengers_parent_id) {
            $passengers_cat_id = tigon_dms_get_existing_category($passengers_normalized, $passengers_parent_id);
            if ($passengers_cat_id) {
                $categories[] = $passengers_cat_id;
            }
        }
    }
    
    // 6. TIGON Dealership category
    if (!empty($store_id)) {
        $store_data = DMS_API::get_city_and_state_by_store_id($store_id);
        $city = $store_data['city'] ?? '';
        $state = $store_data['state'] ?? '';
        
        if (!empty($city) && !empty($state)) {
            $dealership_name = 'TIGON Golf Carts ' . $city . ' ' . $state;
            $dealership_parent_id = tigon_dms_get_existing_category('TIGON Dealership', 0);
            if ($dealership_parent_id) {
                $dealership_cat_id = tigon_dms_get_existing_category($dealership_name, $dealership_parent_id);
                if ($dealership_cat_id) {
                    $categories[] = $dealership_cat_id;
                }
            }
        }
    }
    
    // Assign all categories to product
    if (!empty($categories)) {
        wp_set_object_terms($product_id, $categories, 'product_cat');
    }
    
    // Set DMS meta
    update_post_meta($product_id, '_dms_cart_id', sanitize_text_field($cart_id));
    update_post_meta($product_id, '_dms_payload', wp_json_encode($cart_data));

    // Location watermark badge (_tigonwm) derived from this cart's actual location,
    // so Lecanto products show "Lecanto FL" instead of falling back to the
    // WooCommerce store-location default.
    update_post_meta($product_id, '_tigonwm', tigon_dms_get_watermark_text($cart_data));

    // Store parsed DMS cart data in structured meta
    update_post_meta($product_id, '_dms_cart_specs', $specs);
    update_post_meta($product_id, '_dms_cart_images', $images);
    update_post_meta($product_id, '_dms_cart_warranty', $warranty);
    
    // Price fields for WooCommerce compatibility
    update_post_meta($product_id, '_regular_price', floatval($price));
    update_post_meta($product_id, '_price', floatval($price));
    
    // Enable shipping (not virtual)
    update_post_meta($product_id, '_virtual', 'no');
    update_post_meta($product_id, '_downloadable', 'no');
    
    // Placeholder weight/dimensions for shipping calculations
    update_post_meta($product_id, '_weight', '500');  // 500 lbs
    update_post_meta($product_id, '_length', '96');   // 8 feet
    update_post_meta($product_id, '_width', '48');    // 4 feet
    update_post_meta($product_id, '_height', '72');   // 6 feet
    
    // Stock management
    update_post_meta($product_id, '_manage_stock', 'no');
    update_post_meta($product_id, '_stock_status', 'instock');
    
    // Additional WooCommerce meta
    update_post_meta($product_id, '_visibility', 'visible'); // Visible in catalog and search
    update_post_meta($product_id, '_sold_individually', 'yes');
    update_post_meta($product_id, '_backorders', 'no');
    
    return $product_id;
}

/**
 * Update existing WooCommerce product with fresh DMS data
 *
 * @param int    $product_id Product ID
 * @param string $title      Product title
 * @param float  $price      Retail price
 * @param array  $cart_data  Full DMS payload
 * @param array  $specs      Parsed specs array
 * @param array  $images     Image URLs array
 * @param array  $warranty   Warranty data array
 * @return int Product ID
 */
function tigon_dms_update_woo_product($product_id, $title, $price, $cart_data, $specs = array(), $images = array(), $warranty = array()) {
    // Normalize title for consistency (e.g., "In" → "-")
    $normalized_title = tigon_dms_normalize_title($title);
    
    // Update post title and slug
    wp_update_post(array(
        'ID'         => $product_id,
        'post_title' => sanitize_text_field($normalized_title),
        'post_name'  => sanitize_title($normalized_title),
    ));
    
    // Update price
    update_post_meta($product_id, '_regular_price', floatval($price));
    update_post_meta($product_id, '_price', floatval($price));
    
    // Update DMS payload
    update_post_meta($product_id, '_dms_payload', wp_json_encode($cart_data));

    // Refresh location watermark badge so store transfers reflect correctly.
    update_post_meta($product_id, '_tigonwm', tigon_dms_get_watermark_text($cart_data));

    // Update parsed DMS cart data in structured meta
    update_post_meta($product_id, '_dms_cart_specs', $specs);
    update_post_meta($product_id, '_dms_cart_images', $images);
    update_post_meta($product_id, '_dms_cart_warranty', $warranty);
    
    // Update product categories (only use existing categories, don't create new ones)
    $categories = array();
    
    // Extract data from cart
    $make = $cart_data['cartType']['make'] ?? '';
    $model = $cart_data['cartType']['model'] ?? '';
    $is_used = isset($cart_data['isUsed']) && $cart_data['isUsed'] === true;
    $drive_train = $cart_data['cartAttributes']['driveTrain'] ?? '';
    $passengers = $cart_data['cartAttributes']['passengers'] ?? '';
    $store_id = $cart_data['cartLocation']['locationId'] ?? '';
    
    // 1. Make + Model categories (keep existing logic)
    if (!empty($make)) {
        $make_cat_id = tigon_dms_get_existing_category($make, 0);
        if ($make_cat_id) {
            $categories[] = $make_cat_id;
            
            // Make+Model subcategory
            if (!empty($model)) {
                $make_model = trim($make . ' ' . $model);
                $model_cat_id = tigon_dms_get_existing_category($make_model, $make_cat_id);
                if ($model_cat_id) {
                    $categories[] = $model_cat_id;
                }
            }
        }
    }
    
    // 2. Condition category (keep existing logic)
    $condition_parent_id = tigon_dms_get_existing_category('Condition', 0);
    if ($condition_parent_id) {
        $condition = $is_used ? 'Used' : 'New';
        $condition_cat_id = tigon_dms_get_existing_category($condition, $condition_parent_id);
        if ($condition_cat_id) {
            $categories[] = $condition_cat_id;
        }
    }
    
    // 3. DriveTrain category
    if (!empty($drive_train)) {
        $drivetrain_parent_id = tigon_dms_get_existing_category('DriveTrain', 0);
        if ($drivetrain_parent_id) {
            // Normalize driveTrain value (e.g., "2X4" -> "2x4", "4X4" -> "4x4")
            $drivetrain_value = strtolower($drive_train);
            $drivetrain_cat_id = tigon_dms_get_existing_category($drivetrain_value, $drivetrain_parent_id);
            if (!$drivetrain_cat_id) {
                // Try uppercase version
                $drivetrain_cat_id = tigon_dms_get_existing_category(strtoupper($drive_train), $drivetrain_parent_id);
            }
            if ($drivetrain_cat_id) {
                $categories[] = $drivetrain_cat_id;
            }
        }
    }
    
    // 4. Location category (State -> City hierarchy)
    if (!empty($store_id)) {
        $store_data = DMS_API::get_city_and_state_by_store_id($store_id);
        $city = $store_data['city'] ?? '';
        $state = $store_data['state'] ?? '';
        
        if (!empty($state)) {
            $location_parent_id = tigon_dms_get_existing_category('Location', 0);
            if ($location_parent_id) {
                // Find state category (child of Location)
                $state_cat_id = tigon_dms_get_existing_category($state, $location_parent_id);
                if ($state_cat_id && !empty($city)) {
                    // Find city category (child of State)
                    $city_cat_id = tigon_dms_get_existing_category($city, $state_cat_id);
                    if ($city_cat_id) {
                        $categories[] = $city_cat_id;
                    }
                }
            }
        }
    }
    
    // 5. Passengers category
    if (!empty($passengers)) {
        // Normalize passengers value (e.g., "6 Passenger" -> "6 seater")
        $passengers_normalized = strtolower(str_replace(' passenger', ' seater', $passengers));
        $passengers_parent_id = tigon_dms_get_existing_category('Passengers', 0);
        if ($passengers_parent_id) {
            $passengers_cat_id = tigon_dms_get_existing_category($passengers_normalized, $passengers_parent_id);
            if ($passengers_cat_id) {
                $categories[] = $passengers_cat_id;
            }
        }
    }
    
    // 6. TIGON Dealership category
    if (!empty($store_id)) {
        $store_data = DMS_API::get_city_and_state_by_store_id($store_id);
        $city = $store_data['city'] ?? '';
        $state = $store_data['state'] ?? '';
        
        if (!empty($city) && !empty($state)) {
            $dealership_name = 'TIGON Golf Carts ' . $city . ' ' . $state;
            $dealership_parent_id = tigon_dms_get_existing_category('TIGON Dealership', 0);
            if ($dealership_parent_id) {
                $dealership_cat_id = tigon_dms_get_existing_category($dealership_name, $dealership_parent_id);
                if ($dealership_cat_id) {
                    $categories[] = $dealership_cat_id;
                }
            }
        }
    }
    
    // Assign all categories to product
    if (!empty($categories)) {
        wp_set_object_terms($product_id, $categories, 'product_cat');
    }
    
    // Ensure product is visible in catalog (remove any exclude-from-catalog terms)
    // This ensures synced products appear on inventory page
    $current_visibility = wp_get_object_terms($product_id, 'product_visibility', array('fields' => 'slugs'));
    if (is_array($current_visibility) && in_array('exclude-from-catalog', $current_visibility)) {
        // Remove exclude-from-catalog and set to visible
        $new_visibility = array_filter($current_visibility, function($term) {
            return $term !== 'exclude-from-catalog';
        });
        if (empty($new_visibility) || !in_array('visible', $new_visibility)) {
            $new_visibility[] = 'visible';
        }
        wp_set_object_terms($product_id, array_values($new_visibility), 'product_visibility');
    } elseif (empty($current_visibility) || !in_array('visible', $current_visibility)) {
        // No visibility set or not visible - set to visible
        wp_set_object_terms($product_id, array('visible'), 'product_visibility');
    }
    
    // Update visibility meta to ensure compatibility
    update_post_meta($product_id, '_visibility', 'visible');
    
    return $product_id;
}

/**
 * ============================================================================
 * WooCommerce Product Tabs: Inject DMS Cart Data
 * ============================================================================
 * 
 * Hooks into woocommerce_product_tabs to override the existing Custom Data
 * tab (or Additional Information tab) and render DMS cart specifications,
 * images, and window sticker PDF link.
 * 
 * Only affects products with _dms_cart_id meta. Non-DMS products are
 * unaffected and continue to use default tab behavior.
 */

/**
 * Get DMS product data from post meta (with fallback parsing)
 * 
 * @param int $product_id Product ID
 * @return array Array with 'specs', 'images', 'warranty' keys
 */
function tigon_dms_get_dms_product_data($product_id) {
    $specs = get_post_meta($product_id, '_dms_cart_specs', true);
    $images = get_post_meta($product_id, '_dms_cart_images', true);
    $warranty = get_post_meta($product_id, '_dms_cart_warranty', true);

    // Rename legacy spec key after the attribute rename (Location -> Vehicle Location)
    if (is_array($specs) && isset($specs['Location']) && !isset($specs['Vehicle Location'])) {
        $specs['Vehicle Location'] = $specs['Location'];
        unset($specs['Location']);
        update_post_meta($product_id, '_dms_cart_specs', $specs);
    }

    // Fallback: If structured meta doesn't exist, parse from full payload
    if (empty($specs) || empty($images)) {
        $payload_json = get_post_meta($product_id, '_dms_payload', true);
        if (!empty($payload_json)) {
            $cart_data = json_decode($payload_json, true);
            if (is_array($cart_data) && !empty($cart_data)) {
                if (empty($specs)) {
                    $specs = tigon_dms_parse_cart_specs($cart_data);
                    update_post_meta($product_id, '_dms_cart_specs', $specs);
                }
                if (empty($images)) {
                    $images = tigon_dms_parse_cart_images($cart_data);
                    update_post_meta($product_id, '_dms_cart_images', $images);
                }
                if (empty($warranty)) {
                    $warranty = tigon_dms_parse_cart_warranty($cart_data);
                    update_post_meta($product_id, '_dms_cart_warranty', $warranty);
                }
            }
        }
    }
    
    return array(
        'specs'    => is_array($specs) ? $specs : array(),
        'images'   => is_array($images) ? $images : array(),
        'warranty' => is_array($warranty) ? $warranty : array(),
    );
}


/**
 * Override WooCommerce product tabs to inject DMS data as separate tabs
 * 
 * For DMS products: Remove "Custom Data" tab and add "Description" and "Additional Information" tabs
 * 
 * @param array $tabs Existing product tabs
 * @return array Modified tabs array
 */
function tigon_dms_override_product_tabs($tabs) {
    global $product;
    
    // Only process on single product pages
    if (!is_product() || !$product) {
        return $tabs;
    }
    
    // Check if this is a DMS product
    $product_id = $product->get_id();
    $cart_id = get_post_meta($product_id, '_dms_cart_id', true);
    
    if (empty($cart_id)) {
        // Not a DMS product - return tabs unchanged
        return $tabs;
    }
    
    // Remove existing tabs that we're replacing
    $tabs_to_remove = array('custom_data', 'specifications', 'specs', 'additional_information', 'dms_custom_data');
    foreach ($tabs_to_remove as $tab_key) {
        if (isset($tabs[$tab_key])) {
            unset($tabs[$tab_key]);
        }
    }
    
    // Add "Description" tab with DMS specs
    $tabs['dms_description'] = array(
        'title'    => __('Description', 'tigon-dms-connect'),
        'priority' => 10,
        'callback' => function() use ($product_id) {
            tigon_dms_render_description_tab($product_id);
        },
    );
    
    // Add "Additional Information" tab with detailed features
    $tabs['dms_additional_info'] = array(
        'title'    => __('Additional Information', 'tigon-dms-connect'),
        'priority' => 20,
        'callback' => function() use ($product_id) {
            tigon_dms_render_additional_info_tab($product_id);
        },
    );
    
    return $tabs;
}
add_filter('woocommerce_product_tabs', 'tigon_dms_override_product_tabs', 98);

/**
 * Render the Description tab content (main specs from first image)
 * 
 * @param int $product_id Product ID
 */
function tigon_dms_render_description_tab($product_id) {
    $dms_data = tigon_dms_get_dms_product_data($product_id);
    $specs = $dms_data['specs'];
    
    // Main specs fields for Description tab (from /get-cart-by-id API)
    $main_fields = array(
        'Make', 'Model', 'Year', 'Street Legal', 'Color', 'Seat Color', 
        'Tires', 'Rims', 'Drivetrain', 'Passengers',
        'Battery Type', 'Battery Brand', 'Battery Year', 'Capacity', 'Battery Voltage', 'Battery Warranty',
        'Engine', 'Horsepower', 'Stroke',
        'Warranty'
    );
    
    // Build main_specs in the correct order from main_fields
    $main_specs = array();
    if (!empty($specs)) {
        // Process in order defined in main_fields array
        foreach ($main_fields as $field_key) {
            if (isset($specs[$field_key]) && !empty($specs[$field_key])) {
                $main_specs[$field_key] = $specs[$field_key];
            }
        }
    }
    
    if (empty($main_specs)) {
        echo '<p>' . esc_html__('No description data available.', 'tigon-dms-connect') . '</p>';
        return;
    }
    ?>
    <div class="dms-description-tab">
        <table class="dms-specs-table dms-additional-info-table">
            <tbody>
                <?php foreach ($main_specs as $label => $value): ?>
                    <tr>
                        <td class="dms-specs-feature"><strong><?php echo esc_html(strtoupper($label)); ?></strong></td>
                        <td class="dms-specs-description">
                            <?php
                            // Red link styling for brand names (Make is always red, Model only if Denago)
                            if ($label === 'Make' || ($label === 'Model' && strpos(strtolower($value), 'denago') !== false)) {
                                echo '<span class="dms-brand-link">' . esc_html($value) . '</span>';
                            } else {
                                echo esc_html($value);
                            }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    tigon_dms_output_tab_styles();
}

/**
 * Render the Additional Information tab content (detailed features from second image)
 * 
 * @param int $product_id Product ID
 */
function tigon_dms_render_additional_info_tab($product_id) {
    $dms_data = tigon_dms_get_dms_product_data($product_id);
    $specs = $dms_data['specs'];
    
    // Additional info fields (from /get-cart-by-id API) - all fields not in Description tab
    $additional_fields = array(
        'Sound System', 'Lift Kit', 'Receiver Hitch', 'Extended Top',
        'Vehicle Power', 'Vehicle Status', 
        'Serial Number', 'VIN', 'Odometer', 'Hours',
        'Vehicle Location', 'Year of Vehicle',
        'Drivetrain', 'Passengers' // These may appear in both tabs if needed
    );
    
    // Main fields that go to Description tab (exclude from Additional Info)
    $main_fields = array(
        'Make', 'Model', 'Year', 'Street Legal', 'Color', 'Seat Color', 
        'Tires', 'Rims', 'Drivetrain', 'Passengers',
        'Battery Type', 'Battery Brand', 'Battery Year', 'Capacity', 'Battery Voltage', 'Battery Warranty',
        'Engine', 'Horsepower', 'Stroke',
        'Warranty'
    );
    
    $additional_info = array();
    if (!empty($specs)) {
        foreach ($specs as $key => $value) {
            // Include if it's in additional_fields AND not in main_fields (to avoid duplicates)
            if (in_array($key, $additional_fields) && !in_array($key, $main_fields)) {
                $additional_info[$key] = $value;
            }
        }
    }
    ?>
    <div class="dms-additional-info-tab">
        <table class="dms-specs-table dms-additional-info-table">
            <tbody>
                <?php foreach ($additional_info as $label => $value): ?>
                    <?php if (!empty($value)): ?>
                        <tr>
                            <td class="dms-specs-feature"><strong><?php echo esc_html(strtoupper($label)); ?></strong></td>
                            <td class="dms-specs-description">
                                <?php
                                // Red link styling for certain values (matching the image)
                                $is_red_link = false;
                                if (strpos(strtolower($value), 'seater') !== false ||
                                    $value === 'Yes' || $value === 'No' ||
                                    strpos(strtolower($value), 'day') !== false ||
                                    strpos(strtolower($value), 'inch') !== false ||
                                    strpos(strtolower($value), 'local') !== false ||
                                    strpos(strtolower($value), 'terrain') !== false ||
                                    strpos(strtolower($value), 'electric') !== false ||
                                    strpos(strtolower($value), 'new') !== false ||
                                    strpos(strtolower($value), 'golf cart') !== false ||
                                    strpos(strtolower($value), 'vehicle') !== false ||
                                    strpos(strtolower($value), 'year') !== false ||
                                    is_numeric($value)) {
                                    $is_red_link = true;
                                }
                                
                                if ($is_red_link) {
                                    echo '<span class="dms-brand-link">' . esc_html($value) . '</span>';
                                } else {
                                    echo esc_html($value);
                                }
                                ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    tigon_dms_output_tab_styles();
}

/**
 * Output shared CSS styles for DMS tabs (only once per page)
 */
function tigon_dms_output_tab_styles() {
    static $styles_output = false;
    if ($styles_output) {
        return;
    }
    $styles_output = true;
    ?>
    <style>
        /* DMS Specs Table Styling */
        .dms-specs-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            margin-bottom: 20px;
            border: 1px solid #d32f2f;
        }
        
        .dms-specs-table thead {
            background-color: #4a4a4a;
            color: #fff;
        }
        
        .dms-specs-table th {
            padding: 12px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            border: 1px solid #333;
        }
        
        .dms-specs-table thead .dms-specs-feature,
        .dms-specs-table thead .dms-specs-description {
            background-color: #4a4a4a;
        }
        
        .dms-specs-table .dms-specs-feature {
            width: 20%;
        }
        
        .dms-specs-table .dms-specs-description {
            width: 80%;
        }
        
        .dms-specs-table tbody tr {
            border-bottom: 1px solid #d32f2f;
        }
        
        .dms-specs-table tbody tr:last-child {
            border-bottom: none;
        }
        
        .dms-specs-table tbody td {
            padding: 12px 15px;
            vertical-align: top;
            background-color: #fff;
        }
        
        .dms-specs-table tbody td:first-child {
            font-weight: 600;
            color: #333;
            border-right: 1px solid #d32f2f;
        }
        
        .dms-specs-table tbody td:last-child {
            color: #333;
        }
        
        /* Additional Information Table (no header, uppercase labels) */
        .dms-additional-info-table tbody td:first-child {
            background-color: #f5f5f5;
            text-transform: uppercase;
            font-size: 13px;
            letter-spacing: 0.5px;
        }
        
        /* Brand Link Styling (red and underlined) */
        .dms-brand-link {
            color: #d32f2f;
            text-decoration: underline;
        }
        
        /* Images Grid */
        .dms-tab-images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .dms-tab-image-item {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .dms-tab-image-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .dms-tab-image-item img {
            width: 100%;
            height: auto;
            display: block;
        }
        
        /* Window Sticker Link */
        .dms-window-sticker-link {
            color: #0c4774;
            text-decoration: underline;
            font-weight: 500;
        }
        
        .dms-window-sticker-link:hover {
            color: #AF1F31;
        }
    </style>
    <?php
}

/**
 * Flush rewrite rules on activation + create database tables
 */
function tigon_dms_activation() {
    tigon_dms_add_cart_route();
    flush_rewrite_rules();
    // Create DMS Connect database tables (config + cart lists)
    \Tigon\DmsConnect\Core::install();
}
register_activation_hook(__FILE__, 'tigon_dms_activation');

/**
 * Flush rewrite rules on deactivation
 */
function tigon_dms_deactivation() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'tigon_dms_deactivation');

/**
 * ============================================================================
 * ONE-TIME FIX: Recover products hidden from WP Admin
 * ============================================================================
 * 
 * Problem: Products with both `exclude-from-catalog` AND `exclude-from-search`
 * terms in `product_visibility` taxonomy don't appear in WP Admin → Products.
 * 
 * Solution: Remove ONLY `exclude-from-search` term, keep `exclude-from-catalog`.
 * This makes products visible in admin but still hidden from frontend shop.
 * 
 * Access: /wp-admin/admin.php?page=dms-recover-products
 * 
 * SAFE TO REMOVE after running once successfully.
 * ============================================================================
 */
function tigon_dms_add_recovery_menu() {
    add_submenu_page(
        null, // Hidden from menu
        'DMS Recover Products',
        'DMS Recover Products',
        'manage_woocommerce',
        'dms-recover-products',
        'tigon_dms_recover_products_page'
    );
}
add_action('admin_menu', 'tigon_dms_add_recovery_menu');

/**
 * One-time recovery: Remove exclude-from-search term from DMS products
 */
function tigon_dms_recover_products_page() {
    if (!current_user_can('manage_woocommerce')) {
        wp_die('Unauthorized access');
    }
    
    $fixed = array();
    $already_ok = 0;
    
    // Get the term ID for 'exclude-from-search'
    $exclude_search_term = get_term_by('slug', 'exclude-from-search', 'product_visibility');
    
    if (!$exclude_search_term) {
        echo '<div class="wrap"><h1>DMS Product Recovery</h1>';
        echo '<div class="notice notice-warning"><p>Term "exclude-from-search" not found. Nothing to fix.</p></div>';
        echo '</div>';
        return;
    }

    // Find all products that have exclude-from-search term
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_visibility',
                'field'    => 'slug',
                'terms'    => 'exclude-from-search',
            ),
        ),
    );
    
    $product_ids = get_posts($args);
    
    foreach ($product_ids as $product_id) {
        $title = get_the_title($product_id);
        
        // Get current visibility terms
        $current_terms = wp_get_object_terms($product_id, 'product_visibility', array('fields' => 'slugs'));
        
        // Remove only exclude-from-search, keep everything else
        $new_terms = array_filter($current_terms, function($term) {
            return $term !== 'exclude-from-search';
        });
        
        // If terms actually changed, update them
        if (count($new_terms) !== count($current_terms)) {
            wp_set_object_terms($product_id, array_values($new_terms), 'product_visibility');
            $fixed[] = array(
                'id'    => $product_id,
                'title' => $title,
                'kept'  => implode(', ', $new_terms) ?: '(none)',
            );
                } else {
            $already_ok++;
        }
    }
    
    // Output results
    echo '<div class="wrap">';
    echo '<h1>🔧 DMS Product Recovery</h1>';
    
    if (!empty($fixed)) {
        echo '<div class="notice notice-success"><p>';
        echo '<strong>✅ Fixed ' . count($fixed) . ' product(s)!</strong> ';
        echo 'Removed <code>exclude-from-search</code> term. Products should now appear in WP Admin → Products.';
        echo '</p></div>';
        
        echo '<table class="widefat striped" style="max-width: 800px;">';
        echo '<thead><tr><th>ID</th><th>Product Title</th><th>Remaining Terms</th></tr></thead>';
        echo '<tbody>';
        foreach ($fixed as $item) {
            echo '<tr>';
            echo '<td><a href="' . get_edit_post_link($item['id']) . '">' . $item['id'] . '</a></td>';
            echo '<td>' . esc_html($item['title']) . '</td>';
            echo '<td><code>' . esc_html($item['kept']) . '</code></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<div class="notice notice-info"><p>';
        echo '<strong>No products needed fixing.</strong> ';
        if ($already_ok > 0) {
            echo 'Found ' . $already_ok . ' product(s) but they were already OK.';
        } else {
            echo 'No products have the <code>exclude-from-search</code> term.';
        }
        echo '</p></div>';
    }
    
    echo '<p style="margin-top: 20px;">';
    echo '<a href="' . admin_url('edit.php?post_type=product') . '" class="button button-primary">View All Products</a> ';
    echo '<a href="' . admin_url('admin.php?page=dms-recover-products') . '" class="button">Run Again</a>';
    echo '</p>';
    
    echo '<hr style="margin-top: 30px;">';
    echo '<p><em>🗑️ This code block is safe to remove after running successfully.</em></p>';
        echo '</div>';
    }

/**
 * ============================================================================
 * ONE-TIME FIX: Normalize DMS product titles
 * ============================================================================
 * 
 * Problem: Some products have titles with " – " (en-dash) or " - " (hyphen)
 * but we want consistent " In " format everywhere for searchability.
 * 
 * Solution: Normalize all titles to use " In " format and update post_name.
 * 
 * Access: /wp-admin/admin.php?page=dms-normalize-titles
 * 
 * SAFE TO REMOVE after running once successfully.
 * ============================================================================
 */
function tigon_dms_add_normalize_menu() {
    add_submenu_page(
        null, // Hidden from menu
        'DMS Normalize Titles',
        'DMS Normalize Titles',
        'manage_woocommerce',
        'dms-normalize-titles',
        'tigon_dms_normalize_titles_page'
    );
}
add_action('admin_menu', 'tigon_dms_add_normalize_menu');

/**
 * One-time migration: Normalize DMS product titles
 */
function tigon_dms_normalize_titles_page() {
    if (!current_user_can('manage_woocommerce')) {
        wp_die('Unauthorized access');
    }
    
    $fixed = array();
    $already_ok = 0;
    
    // Find all products with _dms_cart_id meta (DMS products)
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'meta_query'     => array(
            array(
                'key'     => '_dms_cart_id',
                'compare' => 'EXISTS',
            ),
        ),
    );
    
    $products = get_posts($args);

    foreach ($products as $product) {
        $original_title = $product->post_title;
        $original_slug  = $product->post_name;
        
        // Normalize title
        $normalized_title = tigon_dms_normalize_title($original_title);
        $normalized_slug  = sanitize_title($normalized_title);
        
        // Check if normalization changed anything
        if ($original_title === $normalized_title && $original_slug === $normalized_slug) {
            $already_ok++;
            continue;
        }
        
        // Update post
        wp_update_post(array(
            'ID'         => $product->ID,
            'post_title' => $normalized_title,
            'post_name'  => $normalized_slug,
        ));
        
        $fixed[] = array(
            'id'            => $product->ID,
            'old_title'     => $original_title,
            'new_title'     => $normalized_title,
            'old_slug'      => $original_slug,
            'new_slug'      => $normalized_slug,
        );
    }
    
    // Output results
    echo '<div class="wrap">';
    echo '<h1>🔧 DMS Title Normalization</h1>';
    
    if (!empty($fixed)) {
        echo '<div class="notice notice-success"><p>';
        echo '<strong>✅ Normalized ' . count($fixed) . ' product(s)!</strong> ';
        echo 'Normalized titles to use " In " format and updated slugs.';
        echo '</p></div>';
        
        echo '<table class="widefat striped" style="max-width: 1000px;">';
        echo '<thead><tr><th>ID</th><th>Old Title</th><th>New Title</th><th>New Slug</th></tr></thead>';
        echo '<tbody>';
        foreach ($fixed as $item) {
            echo '<tr>';
            echo '<td><a href="' . get_edit_post_link($item['id']) . '">' . $item['id'] . '</a></td>';
            echo '<td>' . esc_html($item['old_title']) . '</td>';
            echo '<td><strong>' . esc_html($item['new_title']) . '</strong></td>';
            echo '<td><code>' . esc_html($item['new_slug']) . '</code></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<div class="notice notice-info"><p>';
        echo '<strong>No products needed normalizing.</strong> ';
        if ($already_ok > 0) {
            echo 'Found ' . $already_ok . ' DMS product(s) but titles were already normalized.';
        } else {
            echo 'No DMS products found (products with <code>_dms_cart_id</code> meta).';
        }
        echo '</p></div>';
    }
    
    echo '<p style="margin-top: 20px;">';
    echo '<a href="' . admin_url('edit.php?post_type=product') . '" class="button button-primary">View All Products</a> ';
    echo '<a href="' . admin_url('admin.php?page=dms-normalize-titles') . '" class="button">Run Again</a>';
    echo '</p>';
    
    echo '<hr style="margin-top: 30px;">';
    echo '<p><em>🗑️ This code block is safe to remove after running successfully.</em></p>';
        echo '</div>';
    }

/**
 * Add admin menu for updating product titles with ® symbol
 */
function tigon_dms_add_update_titles_menu() {
    add_submenu_page(
        null, // Hidden from menu
        'DMS Update Titles with ®',
        'DMS Update Titles with ®',
        'manage_options',
        'dms-update-titles-reg',
        'tigon_dms_update_titles_reg_page'
    );
}
add_action('admin_menu', 'tigon_dms_add_update_titles_menu');

/**
 * One-time migration: Update DMS product titles to include ® symbol
 */
function tigon_dms_update_titles_reg_page() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized access');
    }
    
    $fixed = array();
    $already_ok = 0;
    $errors = array();
    
    // Find all products with _dms_cart_id meta (DMS products)
    $args = array(
        'post_type'      => 'product',
        'post_status'    => 'any',
        'posts_per_page' => -1,
        'meta_query'     => array(
            array(
                'key'     => '_dms_cart_id',
                'compare' => 'EXISTS',
            ),
        ),
    );
    
    $products = get_posts($args);

    foreach ($products as $product) {
        $original_title = $product->post_title;
        
        // Get DMS payload
        $payload_json = get_post_meta($product->ID, '_dms_payload', true);
        if (empty($payload_json)) {
            $errors[] = 'Product #' . $product->ID . ' has no DMS payload';
            continue;
        }
        
        $cart_data = json_decode($payload_json, true);
        if (empty($cart_data) || !is_array($cart_data)) {
            $errors[] = 'Product #' . $product->ID . ' has invalid DMS payload';
            continue;
        }
        
        // Extract data
        $make = $cart_data['cartType']['make'] ?? '';
        $model = $cart_data['cartType']['model'] ?? '';
        $color = $cart_data['cartAttributes']['cartColor'] ?? '';
        $store_id = $cart_data['cartLocation']['locationId'] ?? '';
        
        // Get location
        $location_string = '';
        if (!empty($store_id)) {
            $location_data = DMS_API::get_city_and_state_by_store_id($store_id);
            $city = $location_data['city'];
            $state = $location_data['state'];
            $location_string = trim($city . ', ' . $state, ', ');
        }
        
        // Build title with ® between make and model
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
        $new_title = trim(implode(' ', $title_parts));
        if (!empty($location_string)) {
            $new_title .= ' In ' . $location_string;
        }
        
        // Normalize title
        $normalized_title = tigon_dms_normalize_title($new_title);
        $normalized_slug = sanitize_title($normalized_title);
        
        // Check if title changed
        if ($original_title === $normalized_title) {
            $already_ok++;
            continue;
        }
        
        // Update post
        $result = wp_update_post(array(
            'ID'         => $product->ID,
            'post_title' => $normalized_title,
            'post_name'  => $normalized_slug,
        ));
        
        if (is_wp_error($result)) {
            $errors[] = 'Product #' . $product->ID . ': ' . $result->get_error_message();
            continue;
        }
        
        $fixed[] = array(
            'id'        => $product->ID,
            'old_title' => $original_title,
            'new_title' => $normalized_title,
            'new_slug'  => $normalized_slug,
        );
    }
    
    // Output results
    echo '<div class="wrap">';
    echo '<h1>🔧 DMS Update Titles with ® Symbol</h1>';
    
    if (!empty($errors)) {
        echo '<div class="notice notice-error"><p>';
        echo '<strong>⚠️ Errors:</strong>';
        echo '<ul>';
        foreach ($errors as $error) {
            echo '<li>' . esc_html($error) . '</li>';
        }
        echo '</ul>';
        echo '</p></div>';
    }
    
    if (!empty($fixed)) {
        echo '<div class="notice notice-success"><p>';
        echo '<strong>✅ Updated ' . count($fixed) . ' product(s)!</strong> ';
        echo 'Added ® symbol between make and model in titles.';
        echo '</p></div>';
        
        echo '<table class="widefat striped" style="max-width: 1000px;">';
        echo '<thead><tr><th>ID</th><th>Old Title</th><th>New Title</th><th>New Slug</th></tr></thead>';
        echo '<tbody>';
        foreach ($fixed as $item) {
            echo '<tr>';
            echo '<td><a href="' . get_edit_post_link($item['id']) . '">' . $item['id'] . '</a></td>';
            echo '<td>' . esc_html($item['old_title']) . '</td>';
            echo '<td><strong>' . esc_html($item['new_title']) . '</strong></td>';
            echo '<td><code>' . esc_html($item['new_slug']) . '</code></td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo '<div class="notice notice-info"><p>';
        echo '<strong>No products needed updating.</strong> ';
        if ($already_ok > 0) {
            echo 'Found ' . $already_ok . ' DMS product(s) but titles already have ® symbol.';
        } else {
            echo 'No DMS products found (products with <code>_dms_cart_id</code> meta).';
        }
        echo '</p></div>';
    }
    
    echo '<p style="margin-top: 20px;">';
    echo '<a href="' . admin_url('edit.php?post_type=product') . '" class="button button-primary">View All Products</a> ';
    echo '<a href="' . admin_url('admin.php?page=dms-update-titles-reg') . '" class="button">Run Again</a>';
    echo '</p>';
    
    echo '<hr style="margin-top: 30px;">';
    echo '<p><em>🗑️ This code block is safe to remove after running successfully.</em></p>';
    echo '</div>';
}

/**
 * ============================================================================
 * WooCommerce Product Page: Inject DMS Cart Data via JavaScript
 * ============================================================================
 * 
 * Enqueues JS that injects DMS cart images and window sticker into the
 * existing WooCommerce Product Add-Ons widget, and moves Extended Warranty.
 * 
 * Final order:
 * 1. [Woo Add-ons] (existing)
 * 2. [DMS Cart Images]
 * 3. [Cart Window Sticker PDF]
 * 4. [Extended Warranty Add-on] (moved)
 */

/**
 * Enqueue DMS injection script on WooCommerce product pages
 */
function tigon_dms_enqueue_woo_inject_script() {
    // Only on single product pages (frontend)
    if (!is_singular('product') || is_admin()) {
        return;
    }
    
    global $post;
    if (!$post) {
        return;
    }
    
    // Check if this is a DMS product
    $cart_id = get_post_meta($post->ID, '_dms_cart_id', true);
    if (empty($cart_id)) {
        return;
    }
    
    // Get DMS payload
    $payload_json = get_post_meta($post->ID, '_dms_payload', true);
    if (empty($payload_json)) {
        return;
    }
    
    $cart_data = json_decode($payload_json, true);
    if (empty($cart_data) || !is_array($cart_data)) {
        return;
    }
    
    // Extract image URLs
    $image_urls = $cart_data['imageUrls'] ?? array();
    
    // Enqueue API service first (dependency for dms-woo-inject)
    wp_enqueue_script(
        'dms-api-service-js',
        TIGON_DMS_PLUGIN_URL . 'assets/js/dms-api-service.js',
        array('jquery'),
        filemtime(TIGON_DMS_PLUGIN_DIR . 'assets/js/dms-api-service.js'),
        true
    );
    
    // Enqueue the injection script
    wp_enqueue_script(
        'dms-woo-inject',
        TIGON_DMS_PLUGIN_URL . 'assets/js/dms-woo-inject.js',
        array('jquery', 'dms-api-service-js'), // Dependencies: jQuery and API service
        filemtime(TIGON_DMS_PLUGIN_DIR . 'assets/js/dms-woo-inject.js'),
        true // Load in footer
    );
    
    // Pass data to JavaScript
    wp_localize_script('dms-woo-inject', 'dmsWooData', array(
        'cartId'    => $cart_id,
        'imageUrls' => $image_urls,
    ));
}
add_action('wp_enqueue_scripts', 'tigon_dms_enqueue_woo_inject_script');

/**
 * Add data attribute to Extended Warranty add-on for easier JS targeting
 * 
 * This filter adds data-addon="extended-warranty" to the add-on wrapper
 * when the add-on name contains "warranty" (case-insensitive).
 */
function tigon_dms_addon_field_class($classes, $addon, $i) {
    $addon_name = strtolower($addon['name'] ?? '');
    
    // Check if this is the Extended Warranty add-on
    if (strpos($addon_name, 'warranty') !== false || strpos($addon_name, 'extended') !== false) {
        $classes .= ' dms-extended-warranty-addon';
    }
    
    return $classes;
}
add_filter('woocommerce_product_addons_field_class', 'tigon_dms_addon_field_class', 10, 3);

/**
 * Add data attribute to Extended Warranty add-on wrapper
 */
function tigon_dms_addon_start($addon, $product_id, $type) {
    $addon_name = strtolower($addon['name'] ?? '');
    
    // Check if this is the Extended Warranty add-on
    if (strpos($addon_name, 'warranty') !== false || strpos($addon_name, 'extended') !== false) {
        // Output a data attribute via inline script
        echo '<script>
            (function() {
                var wrapper = document.currentScript.parentElement;
                if (wrapper) {
                    wrapper.setAttribute("data-addon", "extended-warranty");
                }
            })();
        </script>';
    }
}
add_action('woocommerce_product_addon_start', 'tigon_dms_addon_start', 10, 3);

/**
 * Disable caching for pages that display DMS inventory
 * Must run early to prevent page caching before rendering
 * 
 * Defines DONOTCACHEPAGE, DONOTCACHEOBJECT, and DONOTCACHEDB constants
 * to bypass WordPress, hosting, and CDN caching for frontend pages with DMS inventory
 */
function tigon_dms_disable_cache() {
    
    // Only on frontend (not admin)
    if (is_admin()) {
        return;
    }

    // Check if current page displays DMS inventory
    $should_disable_cache = false;
    
    // Homepage always displays DMS inventory
    if (is_front_page()) {
        $should_disable_cache = true;
    }
    // Pages can display DMS inventory via multiple methods
    elseif (is_page()) {
        global $post;
        
        if (!$post) {
            return;
        }
        
        // Check if page has DMS shortcode in content
        if (has_shortcode($post->post_content, 'tigon_dms_carts')) {
            $should_disable_cache = true;
        }
        // Check if page is built with Elementor (could have DMS widget)
        elseif (class_exists('\Elementor\Plugin')) {
            $is_elementor = \Elementor\Plugin::$instance->db->is_built_with_elementor($post->ID);
            if ($is_elementor) {
                // Disable cache for Elementor pages - they may use the DMS widget
                // This is safe since Elementor pages that don't use the widget are minimal impact
                $should_disable_cache = true;
            }
        }
        
        // If template override is enabled, all pages show carts via template
        if (TIGON_DMS_HIDE_DEFAULT_CONTENT) {
            $should_disable_cache = true;
        }
        // If content injection is enabled, all non-Elementor pages show carts
        elseif (!$should_disable_cache) {
            // Content injection adds carts to all pages (unless Elementor)
            $should_disable_cache = true;
        }
    }
    
    // Define constants to disable all caching mechanisms
    if ($should_disable_cache) {
        if (!defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }
        if (!defined('DONOTCACHEOBJECT')) {
            define('DONOTCACHEOBJECT', true);
        }
        if (!defined('DONOTCACHEDB')) {
            define('DONOTCACHEDB', true);
        }
    }
}
// Use 'template_redirect' hook with priority 1 - runs very early in frontend, after query is set but before template loads
// This is early enough for most caching plugins to respect the constants
add_action('template_redirect', 'tigon_dms_disable_cache', 1);

/**
 * Register Elementor Widgets
 * - DMS Carts (homepage/location pages)
 * - DMS Inventory Filtered (inventory page with filters)
 */
function tigon_dms_register_elementor_widget($widgets_manager) {
    // Check if Elementor is available
    if (!class_exists('\Elementor\Widget_Base')) {
        return;
    }
    
    // Load and register DMS Carts widget (existing)
    $widget_file = TIGON_DMS_PLUGIN_DIR . 'includes/class-dms-elementor-widget.php';
    if (file_exists($widget_file)) {
        require_once $widget_file;
        
        if (class_exists('DMS_Elementor_Widget')) {
            // Elementor 3.5+ uses register()
            if (method_exists($widgets_manager, 'register')) {
                $widgets_manager->register(new DMS_Elementor_Widget());
            } 
            // Older Elementor versions use register_widget_type()
            elseif (method_exists($widgets_manager, 'register_widget_type')) {
                $widgets_manager->register_widget_type(new DMS_Elementor_Widget());
            }
        }
    }
    
    // Load and register DMS Inventory Filtered widget (NEW)
    $inventory_widget_file = TIGON_DMS_PLUGIN_DIR . 'includes/class-dms-inventory-widget.php';
    if (file_exists($inventory_widget_file)) {
        require_once $inventory_widget_file;
        
        if (class_exists('DMS_Inventory_Widget')) {
            if (method_exists($widgets_manager, 'register')) {
                $widgets_manager->register(new DMS_Inventory_Widget());
            } elseif (method_exists($widgets_manager, 'register_widget_type')) {
                $widgets_manager->register_widget_type(new DMS_Inventory_Widget());
            }
        }
    }
}
add_action('elementor/widgets/register', 'tigon_dms_register_elementor_widget');

/**
 * ============================================================================
 * DMS INVENTORY FILTERED SHORTCODE
 * ============================================================================
 * 
 * Shortcode: [tigon_dms_inventory_filtered]
 * 
 * Attributes:
 *   show_filters   - "yes" or "no" (default: "yes")
 *   show_pagination - "yes" or "no" (default: "yes")
 *   per_page       - Number of carts per page (default: 20)
 * 
 * Usage:
 *   [tigon_dms_inventory_filtered]
 *   [tigon_dms_inventory_filtered show_filters="no" per_page="12"]
 */
function tigon_dms_inventory_filtered_shortcode($atts) {
    // Parse shortcode attributes
    $atts = shortcode_atts(
        array(
            'show_filters'    => 'yes',
            'show_pagination' => 'yes',
            'per_page'        => 20,
        ),
        $atts,
        'tigon_dms_inventory_filtered'
    );
    
    $show_filters    = ($atts['show_filters'] === 'yes');
    $show_pagination = ($atts['show_pagination'] === 'yes');
    $per_page        = intval($atts['per_page']);
    
    // Load widget class if not already loaded
    $widget_file = TIGON_DMS_PLUGIN_DIR . 'includes/class-dms-inventory-widget.php';
    if (!class_exists('DMS_Inventory_Widget') && file_exists($widget_file)) {
        require_once $widget_file;
    }
    
    // Use output buffering to capture the rendered HTML
    ob_start();
    
    if (class_exists('DMS_Inventory_Widget')) {
        DMS_Inventory_Widget::render_inventory_container($show_filters, $show_pagination, $per_page);
    } else {
        echo '<p>DMS Inventory Widget not available.</p>';
    }
    
    return ob_get_clean();
}
add_shortcode('tigon_dms_inventory_filtered', 'tigon_dms_inventory_filtered_shortcode');

/**
 * Enqueue plugin styles on frontend
 * Note: Also enqueued directly in widget render() and shortcode for Elementor editor compatibility
 */
function tigon_dms_enqueue_assets() {
    // Always enqueue Font Awesome for our plugin
    wp_enqueue_style(
        'dms-font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );
    
    // Add Elementor as dependency to ensure our CSS loads after Elementor's CSS
    $dependencies = array('dms-font-awesome');
    if (defined('ELEMENTOR_VERSION')) {
        $dependencies[] = 'elementor-frontend';
    }
    
    wp_enqueue_style(
        'tigon-dms-connect-style',
        TIGON_DMS_PLUGIN_URL . 'assets/css/dms-bridge.css',
        $dependencies,
        filemtime(TIGON_DMS_PLUGIN_DIR . 'assets/css/dms-bridge.css') // Auto cache-busting
    );
}
add_action('wp_enqueue_scripts', 'tigon_dms_enqueue_assets', 20); // Higher priority to load after Elementor
add_action('elementor/editor/before_enqueue_styles', 'tigon_dms_enqueue_assets', 20); // Elementor editor

/**
 * DISABLED: WooCommerce product integration (was causing fatal errors)
 * This function attempted to load cart data for WooCommerce products,
 * but has been removed because the entire WooCommerce product creation
 * approach was causing site crashes.
 */
// Function and hook removed - not needed


/**
 * Template override (only if HIDE_DEFAULT_CONTENT is enabled)
 * For location pages (/hatfield, /ocean_view)
 */
function tigon_dms_template_override($template) {

    if (is_admin()) {
        return $template;
    }

    // Only override if configured to hide default content
    if (!TIGON_DMS_HIDE_DEFAULT_CONTENT) {
        return $template;
    }

    // Homepage
    if (is_front_page()) {
        $custom = TIGON_DMS_PLUGIN_DIR . 'templates/homepage-carts.php';
        if (file_exists($custom)) {
            return $custom;
        }
        }

    // Location pages
    if (is_page()) {
        $custom = TIGON_DMS_PLUGIN_DIR . 'templates/homepage-carts.php';
        if (file_exists($custom)) {
            return $custom;
        }
    }

    return $template;
}

/**
 * Inject carts into non-Elementor pages (when NOT hiding default content)
 * Elementor pages should use the Elementor widget instead
 */
function tigon_dms_inject_carts($content) {
    
    // Skip if we're hiding default content (full template override)
    if (TIGON_DMS_HIDE_DEFAULT_CONTENT) {
        return $content;
    }

    // Don't inject in admin, feeds, or during excerpt
    if (is_admin() || is_feed() || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    // Only inject on pages and homepage (not posts, archives, etc.)
    if (!is_page() && !is_front_page()) {
        return $content;
    }

    // Skip if page is built with Elementor (widget should be used instead)
    global $post;
    if ($post && class_exists('\Elementor\Plugin')) {
        $is_elementor = \Elementor\Plugin::$instance->db->is_built_with_elementor($post->ID);
        if ($is_elementor) {
            return $content; // Don't inject - let Elementor widget handle it
        }
    }

    // Capture cart output
    ob_start();
    if (class_exists('DMS_Display')) {
        DMS_Display::render_carts();
    }
    $carts_html = ob_get_clean();

    // Append carts after page content
    return $content . $carts_html;
}
add_filter('the_content', 'tigon_dms_inject_carts', 20);

/**
 * Shortcode (optional)
 * Usage: [tigon_dms_carts], [tigon_dms_carts type="new"]
 */
function tigon_dms_shortcode($atts) {
    // Enqueue Font Awesome
    wp_enqueue_style(
        'dms-font-awesome',
        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
        array(),
        '6.5.1'
    );
    
    // Ensure CSS is loaded when shortcode is used
    wp_enqueue_style(
        'tigon-dms-connect-style',
        TIGON_DMS_PLUGIN_URL . 'assets/css/dms-bridge.css',
        array('dms-font-awesome'),
        filemtime(TIGON_DMS_PLUGIN_DIR . 'assets/css/dms-bridge.css') // Auto cache-busting
    );

    if (!class_exists('DMS_Display')) {
        return '';
        }

    $atts = shortcode_atts(array(
        'type' => 'all',
    ), $atts);

    ob_start();
    DMS_Display::render_carts($atts['type']);
    return ob_get_clean();
}
add_shortcode('tigon_dms_carts', 'tigon_dms_shortcode');

/**
 * Change WooCommerce products per page to 20 (5 rows of 4 products)
 * 
 * By default WooCommerce shows 16 products per page (4 rows of 4).
 * This filter increases it to 20 products per page (5 rows of 4).
 */
function tigon_dms_products_per_page($cols) {
    return 20;
}
add_filter('loop_shop_per_page', 'tigon_dms_products_per_page', 20);

/**
 * ============================================================================
 * BACKGROUND INVENTORY SYNC
 * ============================================================================
 * 
 * Syncs DMS inventory to WooCommerce products using:
 * - WP-Cron scheduled sync (configurable interval)
 * - Manual "Sync Inventory" admin action
 * - Paginated /get-carts API endpoint
 * - Image handling (featured + gallery)
 * - Idempotent behavior (no duplicate products)
 */

/**
 * Schedule WP-Cron event for inventory sync
 */
function tigon_dms_schedule_sync()
{
    if (!wp_next_scheduled('tigon_dms_sync_inventory')) {
        $interval = DMS_Sync::get_sync_interval();
        wp_schedule_event(time(), 'tigon_dms_sync_interval', 'tigon_dms_sync_inventory');
    }
}
add_action('wp', 'tigon_dms_schedule_sync');

/**
 * Register custom cron interval for sync
 */
function tigon_dms_add_sync_cron_interval($schedules)
{
    $interval = DMS_Sync::get_sync_interval();
    
    $schedules['tigon_dms_sync_interval'] = array(
        'interval' => $interval * HOUR_IN_SECONDS,
        'display'  => sprintf(__('Every %d hour(s)', 'tigon-dms-connect'), $interval),
    );
    
    return $schedules;
}
add_filter('cron_schedules', 'tigon_dms_add_sync_cron_interval');

/**
 * Handle scheduled sync event
 */
function tigon_dms_run_scheduled_sync()
{
    if (!class_exists('DMS_Sync')) {
        return;
    }
    
    DMS_Sync::sync_inventory();
}
add_action('tigon_dms_sync_inventory', 'tigon_dms_run_scheduled_sync');

/**
 * Reschedule sync when interval changes
 */
function tigon_dms_reschedule_sync()
{
    wp_clear_scheduled_hook('tigon_dms_sync_inventory');
    tigon_dms_schedule_sync();
}

/**
 * Add admin menu for inventory sync
 */
function tigon_dms_add_sync_menu()
{
    add_submenu_page(
        'woocommerce',
        'DMS Inventory Sync',
        'DMS Sync',
        'manage_woocommerce',
        'dms-inventory-sync',
        'tigon_dms_sync_page'
    );
}
add_action('admin_menu', 'tigon_dms_add_sync_menu');

/**
 * Render sync admin page
 */
function tigon_dms_sync_page()
{
    if (!current_user_can('manage_woocommerce')) {
        wp_die('Unauthorized access');
    }
    
    $sync_running = false;
    $sync_results = null;
    $sync_interval = DMS_Sync::get_sync_interval();
    
    // Handle manual sync request
    if (isset($_POST['dms_manual_sync']) && check_admin_referer('dms_manual_sync', 'dms_sync_nonce')) {
        $sync_running = true;
        
        // Run sync
        if (class_exists('DMS_Sync')) {
            $sync_results = DMS_Sync::sync_inventory();
        } else {
            $sync_results = array(
                'success' => false,
                'message' => 'DMS_Sync class not found',
            );
        }
    }
    
    // Handle interval update
    if (isset($_POST['dms_update_interval']) && check_admin_referer('dms_update_interval', 'dms_interval_nonce')) {
        $new_interval = isset($_POST['sync_interval']) ? (int) $_POST['sync_interval'] : 6;
        $new_interval = max(1, min(168, $new_interval)); // Clamp between 1-168 hours (1 week)
        
        DMS_Sync::set_sync_interval($new_interval);
        tigon_dms_reschedule_sync();
        
        $sync_interval = $new_interval;
        
        echo '<div class="notice notice-success is-dismissible"><p>';
        echo esc_html__('Sync interval updated successfully.', 'tigon-dms-connect');
        echo '</p></div>';
    }
    
    // Get next scheduled sync time
    $next_sync = wp_next_scheduled('tigon_dms_sync_inventory');
    
    ?>
    <div class="wrap">
        <h1><?php echo esc_html__('DMS Inventory Sync', 'tigon-dms-connect'); ?></h1>
        
        <div class="card" style="max-width: 800px;">
            <h2><?php echo esc_html__('Manual Sync', 'tigon-dms-connect'); ?></h2>
            <p><?php echo esc_html__('Manually trigger a full inventory sync from the DMS API. This may take several minutes depending on the number of carts.', 'tigon-dms-connect'); ?></p>
            
            <form method="post" action="">
                <?php wp_nonce_field('dms_manual_sync', 'dms_sync_nonce'); ?>
                <p>
                    <button type="submit" name="dms_manual_sync" class="button button-primary button-large">
                        <?php echo esc_html__('Sync Inventory Now', 'tigon-dms-connect'); ?>
                    </button>
                </p>
            </form>
            
            <?php if ($sync_running && $sync_results): ?>
                <?php if ($sync_results['success']): ?>
                    <?php $stats = $sync_results['stats']; ?>
                    <div class="notice notice-success" style="margin-top: 15px;">
                        <h3><?php echo esc_html__('Sync Completed', 'tigon-dms-connect'); ?></h3>
                        <ul>
                            <li><strong><?php echo esc_html__('Total carts processed:', 'tigon-dms-connect'); ?></strong> <?php echo esc_html($stats['total']); ?></li>
                            <li><strong><?php echo esc_html__('Products created:', 'tigon-dms-connect'); ?></strong> <?php echo esc_html($stats['created']); ?></li>
                            <li><strong><?php echo esc_html__('Products updated:', 'tigon-dms-connect'); ?></strong> <?php echo esc_html($stats['updated']); ?></li>
                            <li><strong><?php echo esc_html__('Skipped:', 'tigon-dms-connect'); ?></strong> <?php echo esc_html($stats['skipped']); ?></li>
                            <li><strong><?php echo esc_html__('Errors:', 'tigon-dms-connect'); ?></strong> <?php echo esc_html($stats['errors']); ?></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <div class="notice notice-error" style="margin-top: 15px;">
                        <p><strong><?php echo esc_html__('Sync Failed:', 'tigon-dms-connect'); ?></strong> <?php echo esc_html($sync_results['message'] ?? 'Unknown error'); ?></p>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
        
        <div class="card" style="max-width: 800px; margin-top: 20px;">
            <h2><?php echo esc_html__('Scheduled Sync', 'tigon-dms-connect'); ?></h2>
            
            <form method="post" action="">
                <?php wp_nonce_field('dms_update_interval', 'dms_interval_nonce'); ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="sync_interval"><?php echo esc_html__('Sync Interval (hours)', 'tigon-dms-connect'); ?></label>
                        </th>
                        <td>
                            <input type="number" id="sync_interval" name="sync_interval" value="<?php echo esc_attr($sync_interval); ?>" min="1" max="168" step="1" class="small-text">
                            <p class="description"><?php echo esc_html__('How often to automatically sync inventory (1-168 hours).', 'tigon-dms-connect'); ?></p>
                        </td>
                    </tr>
                    <?php if ($next_sync): ?>
                    <tr>
                        <th scope="row"><?php echo esc_html__('Next Scheduled Sync', 'tigon-dms-connect'); ?></th>
                        <td>
                            <p><?php echo esc_html(date_i18n(get_option('date_format') . ' ' . get_option('time_format'), $next_sync)); ?></p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>
                <p>
                    <button type="submit" name="dms_update_interval" class="button button-primary">
                        <?php echo esc_html__('Update Interval', 'tigon-dms-connect'); ?>
                    </button>
                </p>
            </form>
        </div>
    </div>
    <?php
}

/**
 * Schedule sync on plugin activation
 */
function tigon_dms_activation_sync()
{
    tigon_dms_schedule_sync();
}
register_activation_hook(__FILE__, 'tigon_dms_activation_sync');

/**
 * Clear sync schedule on plugin deactivation
 */
function tigon_dms_deactivation_sync()
{
    wp_clear_scheduled_hook('tigon_dms_sync_inventory');
}
register_deactivation_hook(__FILE__, 'tigon_dms_deactivation_sync');

/**
 * ============================================================================
 * TEMPORARY ADMIN-ONLY SYNC TRIGGER (for testing)
 * ============================================================================
 * 
 * Access via: /wp-admin/?run_dms_inventory_sync=1
 * 
 * This is a temporary trigger for testing the sync functionality.
 * Safe to remove after cron is fully implemented and tested.
 */

/**
 * Callable function to sync inventory (wrapper for DMS_Sync::sync_inventory)
 *
 * Calls /get-carts with pagination, loops until no carts returned,
 * creates/updates WooCommerce products using DMS _id stored in post meta.
 *
 * @return array Sync results with stats
 */
function dms_sync_inventory()
{
    error_log('[DMS Sync] Starting inventory sync...');
    
    if (!class_exists('DMS_Sync')) {
        error_log('[DMS Sync] ERROR: DMS_Sync class not found');
        return array(
            'success' => false,
            'message' => 'DMS_Sync class not found',
        );
    }
    
    if (!class_exists('WooCommerce')) {
        error_log('[DMS Sync] ERROR: WooCommerce is not active');
        return array(
            'success' => false,
            'message' => 'WooCommerce is not active',
        );
    }
    
    error_log('[DMS Sync] Calling DMS_Sync::sync_inventory()...');
    $results = DMS_Sync::sync_inventory();
    
    if ($results['success']) {
        $stats = $results['stats'];
        error_log(sprintf(
            '[DMS Sync] Completed successfully. Total: %d, Created: %d, Updated: %d, Skipped: %d, Errors: %d',
            $stats['total'],
            $stats['created'],
            $stats['updated'],
            $stats['skipped'],
            $stats['errors']
        ));
    } else {
        error_log('[DMS Sync] FAILED: ' . ($results['message'] ?? 'Unknown error'));
    }
    
    return $results;
}

/**
 * Admin-only sync trigger
 * 
 * When a logged-in admin visits /wp-admin/?run_dms_inventory_sync=1
 * Execute dms_sync_inventory() and log progress.
 */
function tigon_dms_admin_sync_trigger()
{
    // Check for query parameter first (works in both admin and frontend for testing)
    if (!isset($_GET['run_dms_inventory_sync']) || $_GET['run_dms_inventory_sync'] !== '1') {
        return;
    }
    
    // Only allow in admin area OR for testing (but still require auth)
    if (!is_admin() && !defined('WP_DEBUG')) {
        return;
    }
    
    // Verify user is logged in and has admin capabilities
    if (!is_user_logged_in()) {
        error_log('[DMS Sync] ERROR: User not logged in');
        wp_die('You must be logged in to run the sync.');
    }
    
    if (!current_user_can('manage_options')) {
        error_log('[DMS Sync] ERROR: Unauthorized access attempt by user: ' . get_current_user_id());
        wp_die('You do not have permission to run the sync.');
    }
    
    // Prevent any redirects or other output
    if (!headers_sent()) {
        header('Content-Type: text/plain; charset=utf-8');
        status_header(200);
    }
    
    // Execute sync
    error_log('[DMS Sync] Admin trigger activated by user: ' . get_current_user_id());
    $results = dms_sync_inventory();
    
    // Display results (simple text output for testing)
    echo "DMS Inventory Sync Results\n";
    echo "==========================\n\n";
    
    if ($results && isset($results['success']) && $results['success']) {
        $stats = $results['stats'];
        echo "SUCCESS\n\n";
        echo "Total carts processed: " . $stats['total'] . "\n";
        echo "Products created: " . $stats['created'] . "\n";
        echo "Products updated: " . $stats['updated'] . "\n";
        echo "Skipped: " . $stats['skipped'] . "\n";
        echo "Errors: " . $stats['errors'] . "\n";
        echo "\nCheck error_log for detailed progress.\n";
    } else {
        echo "FAILED\n\n";
        echo "Error: " . (isset($results['message']) ? $results['message'] : 'Unknown error') . "\n";
        echo "\nCheck error_log for details.\n";
    }
    
    exit;
}
// Hook early on both admin_init and init (fallback) for better compatibility
add_action('admin_init', 'tigon_dms_admin_sync_trigger', 1);
add_action('init', 'tigon_dms_admin_sync_trigger', 1);