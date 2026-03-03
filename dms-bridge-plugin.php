<?php
/**
 * Plugin Name: TIGON DMS Connect
 * Description: TIGON DMS Connect — fetches, imports, maps and displays golf carts from the DMS into WooCommerce.
 * Version: 2.0.0
 * Author: Jaslow Digital | Noah Jaslow
 * Author URI: https://jaslowdigital.com/
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
 * Load schema templates from tigon_dms_config for lazy WooCommerce product creation.
 *
 * @return array<string,string>
 */
function tigon_dms_get_schema_templates() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'tigon_dms_config';

    $defaults = [
        'schema_name'              => '{^make}® {^model} {cartColor} in {city}, {stateAbbr}',
        'schema_slug'              => '{make}-{model}-{cartColor}-seat-{seatColor}-{city}-{state}',
        'schema_image_name'        => '{^make}® {^model} {cartColor} in {city}, {stateAbbr} image',
        'schema_monroney_name'     => '{^make}® {^model} {cartColor} in {city}, {stateAbbr} monroney',
        'schema_description'       => '',
        'schema_short_description' => '',
    ];

    $templates = [];
    foreach ($defaults as $key => $default) {
        $value = $wpdb->get_var( $wpdb->prepare(
            "SELECT option_value FROM {$table_name} WHERE option_name = %s LIMIT 1",
            $key
        ) );
        if ($value === null || $value === '') {
            $value = $default;
        }
        $templates[$key] = $value;
    }

    return $templates;
}

/**
 * Convert a US state name to its two-letter abbreviation.
 *
 * @param string $state_name Full state name (e.g. "Pennsylvania")
 * @return string Two-letter abbreviation (e.g. "PA"), or the original string if not found
 */
function tigon_dms_state_abbreviation($state_name) {
    static $map = [
        'Alabama'=>'AL','Alaska'=>'AK','Arizona'=>'AZ','Arkansas'=>'AR','California'=>'CA',
        'Colorado'=>'CO','Connecticut'=>'CT','Delaware'=>'DE','Florida'=>'FL','Georgia'=>'GA',
        'Hawaii'=>'HI','Idaho'=>'ID','Illinois'=>'IL','Indiana'=>'IN','Iowa'=>'IA',
        'Kansas'=>'KS','Kentucky'=>'KY','Louisiana'=>'LA','Maine'=>'ME','Maryland'=>'MD',
        'Massachusetts'=>'MA','Michigan'=>'MI','Minnesota'=>'MN','Mississippi'=>'MS','Missouri'=>'MO',
        'Montana'=>'MT','Nebraska'=>'NE','Nevada'=>'NV','New Hampshire'=>'NH','New Jersey'=>'NJ',
        'New Mexico'=>'NM','New York'=>'NY','North Carolina'=>'NC','North Dakota'=>'ND','Ohio'=>'OH',
        'Oklahoma'=>'OK','Oregon'=>'OR','Pennsylvania'=>'PA','Rhode Island'=>'RI','South Carolina'=>'SC',
        'South Dakota'=>'SD','Tennessee'=>'TN','Texas'=>'TX','Utah'=>'UT','Vermont'=>'VT',
        'Virginia'=>'VA','Washington'=>'WA','West Virginia'=>'WV','Wisconsin'=>'WI','Wyoming'=>'WY',
        'District of Columbia'=>'DC',
    ];
    $name = trim($state_name);
    if (strlen($name) === 2) {
        return strtoupper($name);
    }
    return $map[ucwords(strtolower($name))] ?? $name;
}

/**
 * Apply user-configured field mappings from the admin Field Mapping page
 * to a product after all built-in mappings have been set.
 *
 * @param int   $product_id  WooCommerce product ID.
 * @param array $cart_data   Full DMS cart payload.
 */
function tigon_dms_apply_custom_mappings($product_id, array $cart_data) {
    if (!class_exists('\Tigon\DmsConnect\Admin\Field_Mapping')) {
        return;
    }

    $resolved = \Tigon\DmsConnect\Admin\Field_Mapping::apply($cart_data);

    // Apply postmeta overrides
    foreach ($resolved['postmeta'] as $key => $value) {
        update_post_meta($product_id, $key, $value);
    }

    // Apply post field overrides
    if (!empty($resolved['post'])) {
        $update = ['ID' => $product_id];
        foreach ($resolved['post'] as $field => $value) {
            $update[$field] = $value;
        }
        wp_update_post($update);
    }

    // Apply taxonomy term assignments
    foreach ($resolved['taxonomy'] as $taxonomy => $term_names) {
        $term_ids = [];
        foreach ($term_names as $term_name) {
            $term = get_term_by('name', $term_name, $taxonomy);
            if (!$term) {
                $term = get_term_by('slug', sanitize_title($term_name), $taxonomy);
            }
            if ($term && !is_wp_error($term)) {
                $term_ids[] = $term->term_id;
            }
        }
        if (!empty($term_ids)) {
            wp_set_object_terms($product_id, $term_ids, $taxonomy, true);
        }
    }
}

/**
 * Build a flat variable map for template substitution from DMS cart data.
 *
 * @param array $cart_data
 * @return array<string,mixed>
 */
function tigon_dms_build_template_variables_from_cart(array $cart_data) {
    $make   = $cart_data['cartType']['make'] ?? '';
    $model  = $cart_data['cartType']['model'] ?? '';
    $year   = $cart_data['cartType']['year'] ?? '';
    $color  = $cart_data['cartAttributes']['cartColor'] ?? '';
    $seat   = $cart_data['cartAttributes']['seatColor'] ?? '';
    $store_id = $cart_data['cartLocation']['locationId'] ?? '';

    $city  = '';
    $state = '';
    $stateAbbr = '';

    if ($store_id && class_exists('DMS_API')) {
        $location_data = DMS_API::get_city_and_state_by_store_id($store_id);
        $city  = $location_data['city'] ?? '';
        $state = $location_data['state'] ?? '';
        $stateAbbr = tigon_dms_state_abbreviation($state);
    }

    $vars = [
        'make'        => $make,
        'model'       => $model,
        'year'        => $year,
        'cartColor'   => $color,
        'seatColor'   => $seat,
        'city'        => $city,
        'state'       => $state,
        'stateAbbr'   => $stateAbbr,
        'retailPrice' => $cart_data['retailPrice'] ?? '',
        'salePrice'   => $cart_data['salePrice'] ?? '',
    ];

    $vars['batteryType']     = $cart_data['battery']['type'] ?? '';
    $vars['batteryVoltage']  = $cart_data['battery']['batteryVoltage'] ?? '';
    $vars['packVoltage']     = $cart_data['battery']['packVoltage'] ?? '';
    $vars['batteryBrand']    = $cart_data['battery']['brand'] ?? '';
    $vars['batteryYear']     = $cart_data['battery']['year'] ?? '';
    $vars['batteryAmpHours'] = $cart_data['battery']['ampHours'] ?? '';

    $vars['isStreetLegal'] = isset($cart_data['title']['isStreetLegal'])
        ? ($cart_data['title']['isStreetLegal'] ? 'Yes' : 'No')
        : '';
    $vars['isElectric'] = isset($cart_data['isElectric'])
        ? ($cart_data['isElectric'] ? 'ELECTRIC' : 'GAS')
        : '';
    $vars['isUsed'] = isset($cart_data['isUsed'])
        ? ($cart_data['isUsed'] ? 'USED' : 'NEW')
        : '';

    $vars['serialNumber'] = $cart_data['serialNo'] ?? '';
    $vars['vinNumber']    = $cart_data['vinNo'] ?? '';

    return $vars;
}

/**
 * Evaluate a schema template ({var} / {^var}) against vars.
 *
 * @param string $template
 * @param array<string,mixed> $vars
 * @param bool $slugify
 * @return string
 */
function tigon_dms_evaluate_template($template, array $vars, $slugify = false) {
    $result = preg_replace_callback('/\{([^}]+)\}/', function ($matches) use ($vars) {
        $key = $matches[1];
        $should_ucwords = false;

        if (strpos($key, '^') === 0) {
            $should_ucwords = true;
            $key = substr($key, 1);
        }

        $value = isset($vars[$key]) ? $vars[$key] : '';
        if (!is_string($value)) {
            $value = (string) $value;
        }

        if ($should_ucwords && $value !== '') {
            $value = ucwords(strtolower($value));
        }

        return $value;
    }, $template);

    $result = trim(preg_replace('/\s+/', ' ', $result));

    if ($slugify) {
        $result = sanitize_title($result);
    }

    return $result;
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
    
    // Location
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
                    $specs['Location'] = implode(', ', $location_parts);
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
    
    // Build title using schema template
    $templates = tigon_dms_get_schema_templates();
    $vars      = tigon_dms_build_template_variables_from_cart($cart_data);
    $name_tpl  = isset($templates['schema_name']) && $templates['schema_name'] !== ''
        ? $templates['schema_name']
        : '{^make}® {^model} {cartColor} in {city}, {stateAbbr}';

    $title = tigon_dms_evaluate_template($name_tpl, $vars, false);
    
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

    // Build template variables from cart data for slug generation
    $templates = tigon_dms_get_schema_templates();
    $vars      = tigon_dms_build_template_variables_from_cart($cart_data);

    // Use slug schema template when available, otherwise derive from title
    $slug_tpl = isset($templates['schema_slug']) && $templates['schema_slug'] !== ''
        ? $templates['schema_slug']
        : '{make}-{model}-{cartColor}-seat-{seatColor}-{city}-{state}';
    $slug = tigon_dms_evaluate_template($slug_tpl, $vars, true);

    $product_id = wp_insert_post(array(
        'post_title'     => sanitize_text_field($normalized_title),
        'post_name'      => $slug,
        'post_status'    => 'publish',
        'post_type'      => 'product',
        'post_content'   => '', // Content comes from DMS payload via template
        'comment_status' => 'open',
        'ping_status'    => 'closed',
        'post_author'    => 3,
    ));
    
    if (is_wp_error($product_id) || !$product_id) {
        return false;
    }
    
    // Set product type to simple
    wp_set_object_terms($product_id, 'simple', 'product_type');
    
    // Make product visible in catalog and featured
    wp_set_object_terms($product_id, array('featured'), 'product_visibility');
    
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

    // 7. Golf Carts top-level category
    $golf_carts_cat_id = tigon_dms_get_existing_category('Golf Carts', 0);
    if ($golf_carts_cat_id) {
        $categories[] = $golf_carts_cat_id;
    }

    // 8. Power type categories (Electric/Gas, battery type, voltage, street legal)
    $is_electric = isset($cart_data['isElectric']) && $cart_data['isElectric'];
    $is_street_legal = isset($cart_data['title']['isStreetLegal']) && $cart_data['title']['isStreetLegal'];

    if ($is_electric) {
        $electric_cat_id = tigon_dms_get_existing_category('Electric', 0);
        if ($electric_cat_id) {
            $categories[] = $electric_cat_id;
        }

        $zev_cat_id = tigon_dms_get_existing_category('Zero Emission Vehicles (ZEVs)', 0);
        if ($zev_cat_id) {
            $categories[] = $zev_cat_id;
        }

        // Battery type category (Lithium / Lead-Acid)
        $battery_type = $cart_data['battery']['type'] ?? '';
        if ($battery_type === 'Lithium') {
            $lithium_cat_id = tigon_dms_get_existing_category('Lithium', 0);
            if ($lithium_cat_id) {
                $categories[] = $lithium_cat_id;
            }
        } elseif ($battery_type === 'Lead') {
            $lead_cat_id = tigon_dms_get_existing_category('Lead-Acid', 0);
            if ($lead_cat_id) {
                $categories[] = $lead_cat_id;
            }
        }

        // Voltage category
        $pack_voltage = $cart_data['battery']['packVoltage'] ?? '';
        if (!empty($pack_voltage)) {
            $voltage_cat_id = tigon_dms_get_existing_category($pack_voltage . ' Volt', 0);
            if ($voltage_cat_id) {
                $categories[] = $voltage_cat_id;
            }
        }

        // Street legal electric categories
        if ($is_street_legal) {
            foreach (['Street Legal', 'Neighborhood Electric Vehicles (NEVs)', 'Battery Electric Vehicles (BEVs)', 'Low Speed Vehicles (LSVs)', 'Medium Speed Vehicles (MSVs)'] as $cat_name) {
                $cat_id = tigon_dms_get_existing_category($cat_name, 0);
                if ($cat_id) {
                    $categories[] = $cat_id;
                }
            }
        }
    } else {
        $gas_cat_id = tigon_dms_get_existing_category('Gas', 0);
        if ($gas_cat_id) {
            $categories[] = $gas_cat_id;
        }
        $ptv_cat_id = tigon_dms_get_existing_category('Personal Transportation Vehicles (PTVs)', 0);
        if ($ptv_cat_id) {
            $categories[] = $ptv_cat_id;
        }
    }

    // 9. Lifted / Non-Lifted category
    $is_lifted = isset($cart_data['cartAttributes']['isLifted']) && $cart_data['cartAttributes']['isLifted'];
    if ($is_lifted) {
        $lifted_cat_id = tigon_dms_get_existing_category('Lifted', 0);
        if ($lifted_cat_id) {
            $categories[] = $lifted_cat_id;
        }
    } else {
        $non_lifted_cat_id = tigon_dms_get_existing_category('NON-Lifted', 0);
        if ($non_lifted_cat_id) {
            $categories[] = $non_lifted_cat_id;
        }
    }

    // 10. Seating categories
    if (!empty($passengers)) {
        if ($passengers === 'Utility') {
            $seater_cat_id = tigon_dms_get_existing_category('2 Seater', 0);
        } else {
            $num_seats = explode(' ', $passengers)[0];
            $seater_cat_id = tigon_dms_get_existing_category($num_seats . ' Seater', 0);
        }
        if (!empty($seater_cat_id)) {
            $categories[] = $seater_cat_id;
        }
    }

    // 10b. Utility Task Vehicles (UTVs) — for Utility passengers
    if (($cart_data['cartAttributes']['passengers'] ?? '') === 'Utility') {
        $utv_cat_id = tigon_dms_get_existing_category('Utility Task Vehicles (UTVs)', 0);
        if ($utv_cat_id) {
            $categories[] = $utv_cat_id;
        }
    }

    // 11. New/Used category
    $new_used_cat_id = tigon_dms_get_existing_category($is_used ? 'Used' : 'New', 0);
    if ($new_used_cat_id) {
        $categories[] = $new_used_cat_id;
    }

    // 12. Inventory status category
    $is_rental = !empty($cart_data['isRental']);
    if ($is_rental) {
        if ($is_used) {
            $inv_cat_id = tigon_dms_get_existing_category('Local Used Rental Inventory', 0);
        } else {
            $inv_cat_id = tigon_dms_get_existing_category('Local New Rental Inventory', 0);
        }
        // Also add Rental category
        $rental_cat_id = tigon_dms_get_existing_category('Rental', 0);
        if ($rental_cat_id) {
            $categories[] = $rental_cat_id;
        }
    } else {
        if ($is_used) {
            $inv_cat_id = tigon_dms_get_existing_category('Local Used Active Inventory', 0);
        } else {
            $inv_cat_id = tigon_dms_get_existing_category('Local New Active Inventory', 0);
        }
    }
    if (!empty($inv_cat_id)) {
        $categories[] = $inv_cat_id;
    }

    // 13. Parent hierarchy categories (Passengers, Drivetrain, Power Source, Batteries, Vehicle Class, Location)
    foreach (array('Passengers', 'DriveTrain', 'Power Source', 'Vehicle Class', 'Location') as $parent_name) {
        $parent_id = tigon_dms_get_existing_category($parent_name, 0);
        if ($parent_id) {
            $categories[] = $parent_id;
        }
    }

    // Batteries parent (electric only)
    if (isset($cart_data['isElectric']) && $cart_data['isElectric']) {
        $batteries_cat_id = tigon_dms_get_existing_category('Batteries', 0);
        if ($batteries_cat_id) {
            $categories[] = $batteries_cat_id;
        }
    }

    // Assign all categories to product
    $categories = array_unique(array_filter($categories));
    if (!empty($categories)) {
        wp_set_object_terms($product_id, $categories, 'product_cat');
    }

    // Set DMS meta
    update_post_meta($product_id, '_dms_cart_id', sanitize_text_field($cart_id));
    update_post_meta($product_id, '_dms_payload', wp_json_encode($cart_data));

    // Store parsed DMS cart data in structured meta
    update_post_meta($product_id, '_dms_cart_specs', $specs);
    update_post_meta($product_id, '_dms_cart_images', $images);
    update_post_meta($product_id, '_dms_cart_warranty', $warranty);

    // SKU (VIN > Serial > Generated fallback)
    $sku = '';
    if (!empty($cart_data['vinNo'])) {
        $sku = $cart_data['vinNo'];
    } elseif (!empty($cart_data['serialNo'])) {
        $sku = $cart_data['serialNo'];
    } else {
        // Resolve city for fallback SKU (store_id may have been looked up earlier for categories)
        $fallback_city = '';
        if (!empty($store_id)) {
            $fb_store_data = DMS_API::get_city_and_state_by_store_id($store_id);
            $fallback_city = $fb_store_data['city'] ?? '';
        }
        $sku = strtoupper(
            substr(preg_replace('/\s/', '', $make), 0, 3) .
            substr(preg_replace('/\s/', '', $model), 0, 3) .
            substr(preg_replace('/\s/', '', $cart_data['cartAttributes']['cartColor'] ?? ''), 0, 3) .
            substr(preg_replace('/\s/', '', $cart_data['cartAttributes']['seatColor'] ?? ''), 0, 3) .
            substr(preg_replace('/\s/', '', $fallback_city), 0, 3)
        );
    }
    if (!empty($sku)) {
        update_post_meta($product_id, '_sku', sanitize_text_field($sku));
    }

    // Price fields for WooCommerce compatibility
    update_post_meta($product_id, '_regular_price', floatval($price));
    update_post_meta($product_id, '_price', floatval($price));

    // Sale price
    $sale_price = $cart_data['salePrice'] ?? '';
    if (!empty($sale_price) && floatval($sale_price) > 0 && floatval($sale_price) < floatval($price)) {
        update_post_meta($product_id, '_sale_price', floatval($sale_price));
        update_post_meta($product_id, '_price', floatval($sale_price));
    }
    
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

    // Google/Facebook product feed meta
    $condition = $is_used ? 'used' : 'new';
    $color = $cart_data['cartAttributes']['cartColor'] ?? '';
    update_post_meta($product_id, '_wc_gla_condition', $condition);
    update_post_meta($product_id, '_wc_gla_brand', strtoupper($make));
    update_post_meta($product_id, '_wc_gla_color', strtoupper($color));
    update_post_meta($product_id, '_wc_gla_pattern', $model);
    // Global Unique ID (GUI) — algorithmic trade ID from SKU (mirrors Database_Object)
    $gui = tigon_dms_compute_gui($sku);
    update_post_meta($product_id, '_global_unique_id', $gui);
    update_post_meta($product_id, '_wc_gla_gtin', '');
    update_post_meta($product_id, '_wc_gla_mpn', $gui);
    update_post_meta($product_id, '_wc_gla_sizeSystem', 'US');
    update_post_meta($product_id, '_wc_facebook_enhanced_catalog_attributes_brand', strtoupper($make));
    update_post_meta($product_id, '_wc_facebook_enhanced_catalog_attributes_color', strtoupper($color));
    update_post_meta($product_id, '_wc_facebook_enhanced_catalog_attributes_condition', $condition);
    update_post_meta($product_id, '_wc_facebook_enhanced_catalog_attributes_pattern', $model);
    update_post_meta($product_id, '_wc_facebook_product_image_source', 'product');
    update_post_meta($product_id, '_wc_facebook_sync_enabled', 'yes');
    update_post_meta($product_id, '_wc_fb_visibility', 'yes');
    update_post_meta($product_id, '_wc_pinterest_condition', $condition);
    update_post_meta($product_id, '_wc_pinterest_google_product_category', 'Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts');

    // Download defaults (mirrors Database_Object)
    update_post_meta($product_id, '_download_limit', '-1');
    update_post_meta($product_id, '_download_expiry', '-1');

    // Apply user-configured field mappings (overrides from admin Field Mapping page)
    tigon_dms_apply_custom_mappings($product_id, $cart_data);

    // Apply rich mapping (tags, descriptions, attributes, taxonomies, SEO)
    tigon_dms_apply_rich_mapping($product_id, $cart_data);

    // Refresh WC product lookup table and caches (mirrors Database_Write_Controller write path)
    tigon_dms_refresh_wc_product_data($product_id);

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
    
    // Update post title, slug, and ensure published status
    wp_update_post(array(
        'ID'          => $product_id,
        'post_title'  => sanitize_text_field($normalized_title),
        'post_name'   => sanitize_title($normalized_title),
        'post_status' => 'publish',
    ));
    
    // Update price
    update_post_meta($product_id, '_regular_price', floatval($price));
    update_post_meta($product_id, '_price', floatval($price));

    // Sale price
    $sale_price = $cart_data['salePrice'] ?? '';
    if (!empty($sale_price) && floatval($sale_price) > 0 && floatval($sale_price) < floatval($price)) {
        update_post_meta($product_id, '_sale_price', floatval($sale_price));
        update_post_meta($product_id, '_price', floatval($sale_price));
    } else {
        delete_post_meta($product_id, '_sale_price');
    }

    // Update DMS payload
    update_post_meta($product_id, '_dms_payload', wp_json_encode($cart_data));
    
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

    // 7. Golf Carts top-level category
    $golf_carts_cat_id = tigon_dms_get_existing_category('Golf Carts', 0);
    if ($golf_carts_cat_id) {
        $categories[] = $golf_carts_cat_id;
    }

    // 8. Power type categories (Electric/Gas, battery type, voltage, street legal)
    $is_electric = isset($cart_data['isElectric']) && $cart_data['isElectric'];
    $is_street_legal = isset($cart_data['title']['isStreetLegal']) && $cart_data['title']['isStreetLegal'];

    if ($is_electric) {
        $electric_cat_id = tigon_dms_get_existing_category('Electric', 0);
        if ($electric_cat_id) {
            $categories[] = $electric_cat_id;
        }

        $zev_cat_id = tigon_dms_get_existing_category('Zero Emission Vehicles (ZEVs)', 0);
        if ($zev_cat_id) {
            $categories[] = $zev_cat_id;
        }

        $battery_type = $cart_data['battery']['type'] ?? '';
        if ($battery_type === 'Lithium') {
            $lithium_cat_id = tigon_dms_get_existing_category('Lithium', 0);
            if ($lithium_cat_id) {
                $categories[] = $lithium_cat_id;
            }
        } elseif ($battery_type === 'Lead') {
            $lead_cat_id = tigon_dms_get_existing_category('Lead-Acid', 0);
            if ($lead_cat_id) {
                $categories[] = $lead_cat_id;
            }
        }

        $pack_voltage = $cart_data['battery']['packVoltage'] ?? '';
        if (!empty($pack_voltage)) {
            $voltage_cat_id = tigon_dms_get_existing_category($pack_voltage . ' Volt', 0);
            if ($voltage_cat_id) {
                $categories[] = $voltage_cat_id;
            }
        }

        if ($is_street_legal) {
            foreach (['Street Legal', 'Neighborhood Electric Vehicles (NEVs)', 'Battery Electric Vehicles (BEVs)', 'Low Speed Vehicles (LSVs)', 'Medium Speed Vehicles (MSVs)'] as $cat_name) {
                $cat_id = tigon_dms_get_existing_category($cat_name, 0);
                if ($cat_id) {
                    $categories[] = $cat_id;
                }
            }
        }
    } else {
        $gas_cat_id = tigon_dms_get_existing_category('Gas', 0);
        if ($gas_cat_id) {
            $categories[] = $gas_cat_id;
        }
        $ptv_cat_id = tigon_dms_get_existing_category('Personal Transportation Vehicles (PTVs)', 0);
        if ($ptv_cat_id) {
            $categories[] = $ptv_cat_id;
        }
    }

    // 9. Lifted / Non-Lifted category
    $is_lifted = isset($cart_data['cartAttributes']['isLifted']) && $cart_data['cartAttributes']['isLifted'];
    if ($is_lifted) {
        $lifted_cat_id = tigon_dms_get_existing_category('Lifted', 0);
        if ($lifted_cat_id) {
            $categories[] = $lifted_cat_id;
        }
    } else {
        $non_lifted_cat_id = tigon_dms_get_existing_category('NON-Lifted', 0);
        if ($non_lifted_cat_id) {
            $categories[] = $non_lifted_cat_id;
        }
    }

    // 10. Seating categories
    if (!empty($passengers)) {
        if ($passengers === 'Utility') {
            $seater_cat_id = tigon_dms_get_existing_category('2 Seater', 0);
        } else {
            $num_seats = explode(' ', $passengers)[0];
            $seater_cat_id = tigon_dms_get_existing_category($num_seats . ' Seater', 0);
        }
        if (!empty($seater_cat_id)) {
            $categories[] = $seater_cat_id;
        }
    }

    // 10b. Utility Task Vehicles (UTVs) — for Utility passengers
    if (($cart_data['cartAttributes']['passengers'] ?? '') === 'Utility') {
        $utv_cat_id = tigon_dms_get_existing_category('Utility Task Vehicles (UTVs)', 0);
        if ($utv_cat_id) {
            $categories[] = $utv_cat_id;
        }
    }

    // 11. New/Used category
    $new_used_cat_id = tigon_dms_get_existing_category($is_used ? 'Used' : 'New', 0);
    if ($new_used_cat_id) {
        $categories[] = $new_used_cat_id;
    }

    // 12. Inventory status category
    $is_rental = !empty($cart_data['isRental']);
    if ($is_rental) {
        if ($is_used) {
            $inv_cat_id = tigon_dms_get_existing_category('Local Used Rental Inventory', 0);
        } else {
            $inv_cat_id = tigon_dms_get_existing_category('Local New Rental Inventory', 0);
        }
        // Also add Rental category
        $rental_cat_id = tigon_dms_get_existing_category('Rental', 0);
        if ($rental_cat_id) {
            $categories[] = $rental_cat_id;
        }
    } else {
        if ($is_used) {
            $inv_cat_id = tigon_dms_get_existing_category('Local Used Active Inventory', 0);
        } else {
            $inv_cat_id = tigon_dms_get_existing_category('Local New Active Inventory', 0);
        }
    }
    if (!empty($inv_cat_id)) {
        $categories[] = $inv_cat_id;
    }

    // 13. Parent hierarchy categories (Passengers, Drivetrain, Power Source, Batteries, Vehicle Class, Location)
    foreach (array('Passengers', 'DriveTrain', 'Power Source', 'Vehicle Class', 'Location') as $parent_name) {
        $parent_id = tigon_dms_get_existing_category($parent_name, 0);
        if ($parent_id) {
            $categories[] = $parent_id;
        }
    }

    // Batteries parent (electric only)
    if (isset($cart_data['isElectric']) && $cart_data['isElectric']) {
        $batteries_cat_id = tigon_dms_get_existing_category('Batteries', 0);
        if ($batteries_cat_id) {
            $categories[] = $batteries_cat_id;
        }
    }

    // Assign all categories to product
    $categories = array_unique(array_filter($categories));
    if (!empty($categories)) {
        wp_set_object_terms($product_id, $categories, 'product_cat');
    }

    // Ensure product is visible in catalog and featured
    wp_set_object_terms($product_id, array('featured'), 'product_visibility');
    update_post_meta($product_id, '_visibility', 'visible');

    // Google/Facebook product feed meta
    $condition = $is_used ? 'used' : 'new';
    $color = $cart_data['cartAttributes']['cartColor'] ?? '';
    update_post_meta($product_id, '_wc_gla_condition', $condition);
    update_post_meta($product_id, '_wc_gla_brand', strtoupper($make));
    update_post_meta($product_id, '_wc_gla_color', strtoupper($color));
    update_post_meta($product_id, '_wc_gla_pattern', $model);
    update_post_meta($product_id, '_wc_gla_sizeSystem', 'US');
    update_post_meta($product_id, '_wc_facebook_enhanced_catalog_attributes_brand', strtoupper($make));
    update_post_meta($product_id, '_wc_facebook_enhanced_catalog_attributes_color', strtoupper($color));
    update_post_meta($product_id, '_wc_facebook_enhanced_catalog_attributes_condition', $condition);
    update_post_meta($product_id, '_wc_facebook_enhanced_catalog_attributes_pattern', $model);
    update_post_meta($product_id, '_wc_facebook_product_image_source', 'product');
    update_post_meta($product_id, '_wc_facebook_sync_enabled', 'yes');
    update_post_meta($product_id, '_wc_fb_visibility', 'yes');
    update_post_meta($product_id, '_wc_pinterest_condition', $condition);
    update_post_meta($product_id, '_wc_pinterest_google_product_category', 'Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts');

    // Apply user-configured field mappings (overrides from admin Field Mapping page)
    tigon_dms_apply_custom_mappings($product_id, $cart_data);

    // Apply rich mapping (tags, descriptions, attributes, taxonomies, SEO)
    tigon_dms_apply_rich_mapping($product_id, $cart_data);

    // Refresh WC product lookup table and caches (mirrors Database_Write_Controller write path)
    tigon_dms_refresh_wc_product_data($product_id);

    return $product_id;
}

/**
 * ============================================================================
 * Post-Write WooCommerce Refresh (inspired by Database_Write_Controller)
 * ============================================================================
 *
 * After creating/updating a product via direct meta writes, WooCommerce's
 * wc_product_meta_lookup table and object caches may be stale. These helpers
 * ensure WC product queries (price filters, sorting, stock status) work
 * correctly after sync writes.
 */

/**
 * Refresh WooCommerce product lookup table and caches after sync writes.
 *
 * Our sync path uses wp_insert_post/update_post_meta directly rather than
 * the WC Product API, so wc_product_meta_lookup doesn't get updated
 * automatically. This mirrors what Database_Write_Controller should do
 * after write_database_object().
 *
 * @param int $product_id WooCommerce product ID
 */
function tigon_dms_refresh_wc_product_data($product_id) {
    if (!class_exists('WooCommerce')) {
        return;
    }

    // Clear WordPress post cache so fresh meta is read
    clean_post_cache($product_id);

    // Clear WooCommerce product transients (price ranges, counts, etc.)
    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients($product_id);
    }

    // Update WC product lookup table (used for price filters, stock queries, sorting)
    // This table is not updated by direct update_post_meta() calls
    // Note: WC_Data_Store uses __call() magic, so method_exists() won't find
    // update_lookup_table on the wrapper. We call it directly and catch errors.
    if (class_exists('WC_Data_Store')) {
        try {
            $data_store = \WC_Data_Store::load('product');
            $data_store->update_lookup_table($product_id, 'wc_product_meta_lookup');
        } catch (\Exception $e) {
            // Swallow — lookup table will be rebuilt by global refresh after sync
        } catch (\Error $e) {
            // Catch TypeError/Error from missing method — non-fatal
        }
    }
}

/**
 * Handle a product whose DMS cart is no longer in the active inventory.
 *
 * Mirrors Database_Write_Controller::delete_by_id() cleanup but uses a soft
 * approach: sets stock to out-of-stock and moves to draft instead of hard
 * deleting. Optionally removes attachments (images, monroney PDF).
 *
 * @param int  $product_id      WooCommerce product ID
 * @param bool $delete_images   Whether to also delete attached images (default false)
 * @return bool True if product was handled, false on error
 */
function tigon_dms_handle_sold_product($product_id, $delete_images = false) {
    if (!get_post($product_id)) {
        return false;
    }

    // Mark out of stock
    update_post_meta($product_id, '_stock_status', 'outofstock');
    update_post_meta($product_id, '_stock', 0);

    // Move to draft so it no longer appears on frontend
    wp_update_post(array(
        'ID'          => $product_id,
        'post_status' => 'draft',
    ));

    // Remove from catalog visibility
    wp_set_object_terms($product_id, array('exclude-from-catalog', 'exclude-from-search'), 'product_visibility');

    // Optionally clean up attachments (mirrors REST_Routes delete path)
    if ($delete_images) {
        // Delete featured image
        $featured_id = get_post_thumbnail_id($product_id);
        if ($featured_id) {
            wp_delete_post($featured_id, true);
        }

        // Delete gallery images
        $gallery_ids = get_post_meta($product_id, '_product_image_gallery', true);
        if (!empty($gallery_ids)) {
            foreach (explode(',', $gallery_ids) as $img_id) {
                if (!empty($img_id)) {
                    wp_delete_post((int) $img_id, true);
                }
            }
            delete_post_meta($product_id, '_product_image_gallery');
        }

        // Delete monroney sticker attachment
        $monroney_url = get_post_meta($product_id, 'monroney_sticker', true);
        if (!empty($monroney_url)) {
            global $wpdb;
            $monroney_id = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT ID FROM {$wpdb->posts} WHERE guid = %s AND post_type = 'attachment' LIMIT 1",
                    $monroney_url
                )
            );
            if ($monroney_id) {
                wp_delete_post((int) $monroney_id, true);
            }
        }
    }

    // Clean WC lookup table for this product (mirrors Database_Write_Controller)
    global $wpdb;
    $wpdb->delete($wpdb->prefix . 'wc_product_meta_lookup', array('product_id' => $product_id));

    // Refresh caches
    clean_post_cache($product_id);
    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients($product_id);
    }

    return true;
}

/**
 * ============================================================================
 * Rich Product Mapping (inspired by Abstract_Cart)
 * ============================================================================
 *
 * Enhances WooCommerce products with tags, descriptions, product attributes,
 * custom taxonomies, and SEO meta. Mirrors the mapping logic from the
 * class-based import path (Abstract_Cart) for the simpler sync path.
 */

/**
 * Get make name with trademark symbol
 *
 * @param string $make DMS make name
 * @return string Make name with registered symbol
 */
function tigon_dms_get_make_with_symbol($make) {
    $make = trim($make);
    if (empty($make)) {
        return '';
    }
    $upper = strtoupper($make);
    if ($upper === 'SWIFT' || $upper === 'SWIFT EV') {
        return 'Swift EV®';
    }
    if ($upper === 'TARA' || $upper === 'TARA EV') {
        return 'Tara®';
    }
    if ($upper === 'TEKO' || $upper === 'TEKO EV') {
        return 'Teko®';
    }
    if ($upper === 'TOMBERLIN' || $upper === 'TOMBERLIN EV') {
        return 'Tomberlin®';
    }
    if ($upper === 'YAMAHA' || $upper === 'YAMAHA EV') {
        return 'Yamaha®';
    }
    if ($upper === 'STAR' || $upper === 'STAR EV') {
        return 'Star EV®';
    }
    return $make . '®';
}

/**
 * Compute Global Unique ID from SKU (mirrors Database_Object constructor).
 *
 * Converts alphabetic chars to digits via (ord - 65) % 9 + 1, keeps numeric
 * chars as-is, takes rightmost 14 characters, left-pads with zeros to 14.
 *
 * @param string $sku Product SKU (VIN, serial, or generated)
 * @return string 14-character numeric identifier
 */
function tigon_dms_compute_gui($sku) {
    $chars = str_split((string) $sku);
    $mapped = array_map(function ($char) {
        $int = ord($char);
        if ($int - 65 >= 0) {
            return (($int - 65) % 9) + 1;
        }
        return $char;
    }, $chars);
    $gui = substr(implode('', $mapped), -14, 14);
    return str_pad($gui, 14, '0', STR_PAD_LEFT);
}

/**
 * Get a cached Attributes instance for fast term ID lookups.
 *
 * Lazily instantiates the Attributes class once and caches it for the
 * duration of the request. All sync functions use this to avoid hundreds
 * of individual get_term_by() database queries per product.
 *
 * @return \Tigon\DmsConnect\Admin\Attributes|null Attributes instance or null
 */
function tigon_dms_get_attributes_instance() {
    static $instance = null;
    static $attempted = false;

    if (!$attempted) {
        $attempted = true;
        if (class_exists('\Tigon\DmsConnect\Admin\Attributes')) {
            try {
                $instance = new \Tigon\DmsConnect\Admin\Attributes();
            } catch (\Throwable $e) {
                $instance = null;
            }
        }
    }

    return $instance;
}

/**
 * Apply rich product mapping from Abstract_Cart logic
 *
 * @param int   $product_id WooCommerce product ID
 * @param array $cart_data  Full DMS cart payload
 */
function tigon_dms_apply_rich_mapping($product_id, $cart_data) {
    tigon_dms_assign_product_tags($product_id, $cart_data);
    tigon_dms_set_product_descriptions($product_id, $cart_data);
    tigon_dms_assign_product_attributes($product_id, $cart_data);
    tigon_dms_assign_custom_taxonomies($product_id, $cart_data);
    tigon_dms_set_seo_meta($product_id, $cart_data);
    tigon_dms_set_product_fields_meta($product_id, $cart_data);
}

/**
 * Assign product tags mirroring Abstract_Cart::attach_categories_tags()
 *
 * @param int   $product_id WooCommerce product ID
 * @param array $cart_data  Full DMS cart payload
 */
function tigon_dms_assign_product_tags($product_id, $cart_data) {
    $tags = array();
    $make  = $cart_data['cartType']['make'] ?? '';
    $model = $cart_data['cartType']['model'] ?? '';
    $color = $cart_data['cartAttributes']['cartColor'] ?? '';
    $year  = $cart_data['cartType']['year'] ?? '';
    $passengers    = $cart_data['cartAttributes']['passengers'] ?? '';
    $drivetrain    = $cart_data['cartAttributes']['driveTrain'] ?? '2X4';
    $is_used       = !empty($cart_data['isUsed']);
    $is_electric   = !empty($cart_data['isElectric']);
    $is_street_legal = !empty($cart_data['title']['isStreetLegal']);
    $is_lifted     = !empty($cart_data['cartAttributes']['isLifted']);
    $store_id      = $cart_data['cartLocation']['locationId'] ?? '';
    $battery_type  = $cart_data['battery']['type'] ?? '';

    $make_symbol = tigon_dms_get_make_with_symbol($make);

    // Make tag
    if (!empty($make_symbol)) {
        $tags[] = strtoupper($make_symbol);
    }

    // Make + Model tag
    if (!empty($make_symbol) && !empty($model)) {
        $tags[] = strtoupper($make_symbol . ' ' . $model);
    }

    // Color tag
    if (!empty($color)) {
        $tags[] = strtoupper($color);
    }

    // Seats tag
    if (!empty($passengers)) {
        if ($passengers === 'Utility') {
            $tags[] = '2 SEATS';
        } else {
            $num = explode(' ', $passengers)[0];
            if (is_numeric($num)) {
                $tags[] = $num . ' SEATS';
            }
        }
    }

    // Lifted / Non-lifted
    $tags[] = $is_lifted ? 'LIFTED' : 'NON LIFTED';

    // Condition
    $tags[] = $is_used ? 'USED' : 'NEW';

    // Location tags
    if (!empty($store_id)) {
        $store_data = DMS_API::get_city_and_state_by_store_id($store_id);
        $city  = $store_data['city'] ?? '';
        $state = $store_data['state'] ?? '';
        if (!empty($city)) {
            $tags[] = strtoupper($city);
            $tags[] = strtoupper($city) . ' GOLF CART DEALERSHIP';
        }
        if (!empty($state)) {
            $tags[] = strtoupper($state);
            $tags[] = strtoupper($state) . ' GOLF CART DEALERSHIP';
        }
        if (!empty($city) && !empty($state)) {
            $tags[] = strtoupper($city . ' ' . $state) . ' STREET LEGAL DEALERSHIP';
        }
    }

    // Power type tags
    if ($is_electric) {
        $tags[] = 'ELECTRIC';
        if ($battery_type === 'Lead') {
            $tags[] = 'LEAD-ACID';
        } elseif ($battery_type === 'Lithium') {
            $tags[] = 'LITHIUM';
        }
        if ($is_street_legal) {
            $tags[] = 'NEV';
            $tags[] = 'LSV';
            $tags[] = 'MSV';
            $tags[] = 'STREET LEGAL';
        }
    } else {
        $tags[] = 'GAS';
        $tags[] = 'PTV';
    }

    // 0% FINANCING — all vehicles
    $tags[] = '0% FINANCING';

    // Year tag
    if (!empty($year)) {
        $tags[] = strtoupper($year);
    }

    // Passengers tag (e.g. "4 Passenger", "6 Passenger")
    if (!empty($passengers) && $passengers !== 'Utility') {
        $num = explode(' ', $passengers)[0];
        if (is_numeric($num)) {
            $tags[] = $num . ' Passenger';
        }
    }

    // Drivetrain tag (2X4, 4X4, AWD)
    $tags[] = strtoupper($drivetrain);

    // General tags
    $tags[] = 'GOLF CART';
    $tags[] = 'TIGON';
    $tags[] = 'TIGON GOLF CARTS';

    $tags = array_unique(array_filter($tags));
    if (empty($tags)) {
        return;
    }

    // Use Attributes cache for fast term ID resolution
    $attrs = tigon_dms_get_attributes_instance();
    if ($attrs && !empty($attrs->tags)) {
        $tag_ids = array();
        $tag_names_new = array();
        foreach ($tags as $tag) {
            $id = $attrs->tags[strtoupper($tag)] ?? null;
            if ($id !== null) {
                $tag_ids[] = (int) $id;
            } else {
                $tag_names_new[] = $tag;
            }
        }
        if (!empty($tag_ids)) {
            wp_set_object_terms($product_id, $tag_ids, 'product_tag', true);
        }
        if (!empty($tag_names_new)) {
            wp_set_object_terms($product_id, $tag_names_new, 'product_tag', true);
        }
    } else {
        wp_set_object_terms($product_id, $tags, 'product_tag', true);
    }
}

/**
 * Set product descriptions mirroring Abstract_Cart::generate_descriptions()
 *
 * Only sets description/short_description if not already populated,
 * so manual edits are preserved on subsequent syncs.
 *
 * @param int   $product_id WooCommerce product ID
 * @param array $cart_data  Full DMS cart payload
 */
function tigon_dms_set_product_descriptions($product_id, $cart_data) {
    $update = array('ID' => $product_id);

    $current_content = get_post_field('post_content', $product_id);
    if (empty(trim($current_content))) {
        $description = tigon_dms_generate_description_html($cart_data);
        if (!empty($description)) {
            $update['post_content'] = $description;
        }
    }

    $current_excerpt = get_post_field('post_excerpt', $product_id);
    if (empty(trim($current_excerpt))) {
        $short_desc = tigon_dms_generate_short_description_html($cart_data);
        if (!empty($short_desc)) {
            $update['post_excerpt'] = $short_desc;
        }
    }

    if (count($update) > 1) {
        wp_update_post($update);
    }
}

/**
 * Generate rich HTML product description with feature table
 *
 * @param array $cart_data Full DMS cart payload
 * @return string HTML description
 */
function tigon_dms_generate_description_html($cart_data) {
    $make  = $cart_data['cartType']['make'] ?? '';
    $model = $cart_data['cartType']['model'] ?? '';
    $year  = $cart_data['cartType']['year'] ?? '';
    $color = $cart_data['cartAttributes']['cartColor'] ?? '';
    $seat_color    = $cart_data['cartAttributes']['seatColor'] ?? '';
    $tire_type     = $cart_data['cartAttributes']['tireType'] ?? '';
    $rim_size      = $cart_data['cartAttributes']['tireRimSize'] ?? '';
    $is_electric   = !empty($cart_data['isElectric']);
    $is_street_legal = !empty($cart_data['title']['isStreetLegal']);
    $is_lifted     = !empty($cart_data['cartAttributes']['isLifted']);

    $make_symbol = tigon_dms_get_make_with_symbol($make);

    // Build make/model hyperlinks (mirrors Abstract_Cart)
    $make_slug = sanitize_title($make);
    if (strtoupper($make) === 'DENAGO') {
        $make_slug = sanitize_title($make) . '-ev';
    }
    $model_slug = sanitize_title($model);
    $make_link  = '<a href="https://tigongolfcarts.com/' . esc_attr($make_slug) . '">' . esc_html($make_symbol) . '</a>';
    $model_link = '<a href="https://tigongolfcarts.com/' . esc_attr($make_slug) . '/' . esc_attr($model_slug) . '">' . esc_html($model) . '</a>';

    $name = trim($make_symbol . ' ' . $model . ' ' . $color);

    // Build feature table
    $html = '<h2 style="text-align: center;"><strong>' . esc_html($name) . '</strong></h2>';
    $html .= '<table style="text-align: center;"><thead><tr><th>Feature</th><th>Description</th></tr></thead><tbody>';

    $html .= '<tr><td>Make</td><td>' . $make_link . '</td></tr>';
    $html .= '<tr><td>Model</td><td>' . $model_link . '</td></tr>';
    if (!empty($year)) {
        $html .= '<tr><td>Year</td><td>' . esc_html($year) . '</td></tr>';
    }
    if ($is_street_legal) {
        $html .= '<tr><td>Street Legal</td><td>Fully Street Legal</td></tr>';
    }
    $html .= '<tr><td>Color</td><td>' . esc_html($color) . '</td></tr>';
    if (!empty($seat_color)) {
        $html .= '<tr><td>Seat Color</td><td>' . esc_html($seat_color) . '</td></tr>';
    }
    if (!empty($tire_type)) {
        $html .= '<tr><td>Tires</td><td>' . esc_html($tire_type) . '</td></tr>';
    }
    if (!empty($rim_size)) {
        $html .= '<tr><td>Rims</td><td>' . esc_html($rim_size) . '"</td></tr>';
    }

    // Additional features
    $features = array();
    if ($is_lifted) {
        $features[] = '3 Inch Lift Kit';
    }
    if (!empty($cart_data['cartAttributes']['hasSoundSystem']) && $cart_data['cartAttributes']['hasSoundSystem']) {
        $features[] = $make_symbol . ' Sound System';
    }
    foreach (($cart_data['advertising']['cartAddOns'] ?? array()) as $addon) {
        $parts = explode(' ', $addon);
        array_pop($parts); // Remove price suffix
        $features[] = implode(' ', $parts);
    }
    if (!empty($features)) {
        $html .= '<tr><td>Additional Features</td><td>' . esc_html(implode(', ', $features)) . '</td></tr>';
    }

    // Power/Battery/Engine rows
    if ($is_electric) {
        $battery_type = $cart_data['battery']['type'] ?? '';
        $is_dc = !empty($cart_data['battery']['isDC']);
        $acdc = $is_dc ? 'DC' : 'AC';
        $html .= '<tr><td>Battery</td><td>' . esc_html($battery_type . ' ' . $acdc) . ' Battery</td></tr>';

        $battery_brand = $cart_data['battery']['brand'] ?? '';
        if (!empty($battery_brand)) {
            $html .= '<tr><td>Battery Brand</td><td>' . esc_html($battery_brand) . '</td></tr>';
        }
        $battery_year = $cart_data['battery']['year'] ?? '';
        if (!empty($battery_year)) {
            $html .= '<tr><td>Battery Year</td><td>' . esc_html($battery_year) . '</td></tr>';
        }
        $amp_hours = $cart_data['battery']['ampHours'] ?? '';
        if (!empty($amp_hours)) {
            $html .= '<tr><td>Capacity</td><td>' . esc_html($amp_hours) . ' Amp Hours</td></tr>';
        }
        $warranty_len = $cart_data['warrantyLength'] ?? '';
        $batt_warranty = $cart_data['battery']['warrantyLength'] ?? '';
        if (!empty($warranty_len) || !empty($batt_warranty)) {
            $html .= '<tr><td>Warranty</td><td>' . esc_html($warranty_len . ' parts, ' . $batt_warranty . ' battery warranty') . '</td></tr>';
        }
    } else {
        $engine_make = $cart_data['engine']['make'] ?? '';
        if (!empty($engine_make)) {
            $html .= '<tr><td>Engine</td><td>' . esc_html($year . ' ' . $engine_make) . '</td></tr>';
        }
        $stroke = $cart_data['engine']['stroke'] ?? '';
        $hp = $cart_data['engine']['horsepower'] ?? '';
        if (!empty($stroke) && !empty($hp)) {
            $html .= '<tr><td>Specs</td><td>' . esc_html($stroke . ' Stroke, ' . $hp . ' HP') . '</td></tr>';
        }
        $warranty_len = $cart_data['warrantyLength'] ?? '';
        if (!empty($warranty_len)) {
            $html .= '<tr><td>Warranty</td><td>' . esc_html($warranty_len . ' parts and engine warranty') . '</td></tr>';
        }
    }

    $html .= '</tbody></table>';

    return $html;
}

/**
 * Generate short description marketing copy
 *
 * Uses deterministic selection based on cart ID so re-syncs produce
 * consistent copy (unlike Abstract_Cart which uses random_int).
 *
 * @param array $cart_data Full DMS cart payload
 * @return string HTML short description
 */
function tigon_dms_generate_short_description_html($cart_data) {
    $make  = $cart_data['cartType']['make'] ?? '';
    $model = $cart_data['cartType']['model'] ?? '';
    $color = $cart_data['cartAttributes']['cartColor'] ?? '';
    $is_electric   = !empty($cart_data['isElectric']);
    $has_utility   = ($cart_data['cartAttributes']['passengers'] ?? '') === 'Utility';
    $pack_voltage  = $cart_data['battery']['packVoltage'] ?? '';
    $passengers    = $cart_data['cartAttributes']['passengers'] ?? '';

    $make_symbol = tigon_dms_get_make_with_symbol($make);
    $name = trim($make_symbol . ' ' . $model . ' ' . $color);

    // Build hyperlinks
    $make_slug = sanitize_title($make);
    if (strtoupper($make) === 'DENAGO') {
        $make_slug .= '-ev';
    }
    $model_slug = sanitize_title($model);
    $make_link  = '<a href="https://tigongolfcarts.com/' . esc_attr($make_slug) . '">' . esc_html($make_symbol) . '</a>';
    $model_link = '<a href="https://tigongolfcarts.com/' . esc_attr($make_slug) . '/' . esc_attr($model_slug) . '">' . esc_html($model) . '</a>';

    // Deterministic selection based on cart ID
    $cart_id = $cart_data['_id'] ?? '';
    $seed = crc32($cart_id);

    $adjectives = array('elegant', 'unbeatable', 'exceptional', 'versatile', 'dependable', 'stylish', 'eye-catching', 'proven and reliable', 'sleek');
    $adj = $adjectives[abs($seed) % count($adjectives)];

    $intros = array(
        "Introducing the $make_link $model_link from Tigon Golf Carts,",
        "Experience the freedom to explore with this " . esc_html($color) . " $make_link $model_link,",
        "The $make_link $model_link is taking the industry by storm as",
        "Conquer the terrain with the $make_link $model_link in " . esc_html($color) . ",",
        "Take the reigns of the " . esc_html($color) . " $make_link $model_link from Tigon Golf Carts,",
    );
    $intro = $intros[abs($seed >> 4) % count($intros)];

    $outros = array(
        'this ' . $adj . ' cart is perfect for both on and off-course adventures.',
        'the ' . $adj . ' ' . esc_html($make_symbol . ' ' . $model) . ' is the perfect companion for all your journeys.',
        'this ' . $adj . ' machine is a cart you don\'t want to miss!',
        'this ' . $adj . ' vehicle sets a new standard for luxury and efficiency.',
    );
    $outro = $outros[abs($seed >> 8) % count($outros)];

    $html = '<h2 style="text-align: center;">' . esc_html($name) . '</h2>';
    $html .= '<p style="text-align: center;">' . $intro;

    if ($has_utility) {
        $html .= ' a sturdy workhorse ready to help you get the job done.</p>';
        $html .= '<p style="text-align: center;">Featuring a built-in utility bed, the ' . esc_html($model) . ' is highly capable and versatile.';
    } elseif ($is_electric) {
        $html .= ' an elegant powerhouse designed for adventure seekers.</p>';
        $html .= '<p style="text-align: center;">Equipped with a powerful ' . esc_html($pack_voltage) . ' volt electric motor, the ' . esc_html($model) . ' provides a clean, reliable ride without sacrificing performance. ';
    } else {
        $hp = $cart_data['engine']['horsepower'] ?? '';
        $engine_desc = !empty($hp) ? ($hp . ' horsepower') : 'high quality';
        $html .= ' a rugged beast ready to help you take on the world.</p>';
        $html .= '<p style="text-align: center;">With a ' . esc_html($engine_desc) . ' gas engine, the ' . esc_html($model) . ' is a powerhouse of performance. ';
    }

    if (!$has_utility && !empty($passengers)) {
        $num = explode(' ', $passengers)[0];
        if ($num === '6') {
            $html .= 'Capable of carting 6 passengers, ';
        } else {
            $html .= 'Combining rugged durability with sophisticated technology, ';
        }
    } else {
        $html .= 'Combining rugged durability with sophisticated technology, ';
    }

    $html .= $outro . '</p>';

    return $html;
}

/**
 * Assign WooCommerce product attributes mirroring Abstract_Cart::attach_attributes()
 *
 * Only assigns attributes whose taxonomy terms already exist in WooCommerce.
 *
 * @param int   $product_id WooCommerce product ID
 * @param array $cart_data  Full DMS cart payload
 */
function tigon_dms_assign_product_attributes($product_id, $cart_data) {
    $make  = $cart_data['cartType']['make'] ?? '';
    $color = $cart_data['cartAttributes']['cartColor'] ?? '';
    $seat_color    = $cart_data['cartAttributes']['seatColor'] ?? '';
    $passengers    = $cart_data['cartAttributes']['passengers'] ?? '';
    $is_electric   = !empty($cart_data['isElectric']);
    $is_street_legal = !empty($cart_data['title']['isStreetLegal']);
    $is_lifted     = !empty($cart_data['cartAttributes']['isLifted']);
    $has_extended_top = !empty($cart_data['cartAttributes']['hasExtendedTop']);
    $has_sound     = !empty($cart_data['cartAttributes']['hasSoundSystem']);
    $tire_type     = $cart_data['cartAttributes']['tireType'] ?? '';
    $rim_size      = $cart_data['cartAttributes']['tireRimSize'] ?? '';
    $year          = $cart_data['cartType']['year'] ?? '';
    $drive_train   = $cart_data['cartAttributes']['driveTrain'] ?? '2X4';
    $has_hitch     = !empty($cart_data['cartAttributes']['hitch']);
    $is_used       = !empty($cart_data['isUsed']);
    $battery_type  = $cart_data['battery']['type'] ?? '';
    $warranty      = $cart_data['warrantyLength'] ?? '';
    $battery_warranty = $cart_data['battery']['warrantyLength'] ?? '';
    $store_id      = $cart_data['cartLocation']['locationId'] ?? '';
    $has_utility   = $passengers === 'Utility';

    $make_symbol = tigon_dms_get_make_with_symbol($make);
    $make_lower  = sanitize_title(preg_replace('/[®™]/', '', $make_symbol));

    $product_attributes = array();
    $position = 0;

    // Cached Attributes instance for fast O(1) term lookups
    $attrs = tigon_dms_get_attributes_instance();

    // Helper: register attribute and assign terms using Attributes cache or DB fallback
    $set_attr = function($attr_slug, $term_values) use ($product_id, &$product_attributes, &$position, $attrs) {
        $taxonomy = 'pa_' . $attr_slug;
        if (!taxonomy_exists($taxonomy)) {
            return;
        }

        $term_ids = array();
        foreach ((array) $term_values as $term_name) {
            if (empty($term_name)) continue;

            // Fast path: use Attributes pre-loaded attribute options
            if ($attrs && isset($attrs->attributes[$attr_slug]['options'][strtoupper($term_name)])) {
                $term_ids[] = (int) $attrs->attributes[$attr_slug]['options'][strtoupper($term_name)];
                continue;
            }

            // Slow path: individual DB lookup, auto-create if missing
            $term = get_term_by('name', $term_name, $taxonomy);
            if (!$term) {
                $term = get_term_by('slug', sanitize_title($term_name), $taxonomy);
            }
            if ($term && !is_wp_error($term)) {
                $term_ids[] = (int) $term->term_id;
            } else {
                $new_term = wp_insert_term(
                    $term_name,
                    $taxonomy,
                    array('slug' => sanitize_title($term_name))
                );
                if (!is_wp_error($new_term)) {
                    $term_ids[] = (int) $new_term['term_id'];
                    if ($attrs) {
                        $attrs->attributes[$attr_slug]['options'][strtoupper($term_name)] = $new_term['term_id'];
                    }
                }
            }
        }

        if (empty($term_ids)) {
            return;
        }

        // Use Attributes object structure for consistency with class-based import
        if ($attrs && isset($attrs->attributes[$attr_slug]['object'])) {
            $product_attributes[$taxonomy] = $attrs->attributes[$attr_slug]['object'];
            $product_attributes[$taxonomy]['position'] = $position++;
        } else {
            $product_attributes[$taxonomy] = array(
                'name'         => $taxonomy,
                'value'        => '',
                'position'     => $position++,
                'is_visible'   => 1,
                'is_variation' => 0,
                'is_taxonomy'  => 1,
            );
        }
        wp_set_object_terms($product_id, $term_ids, $taxonomy);
    };

    // Battery Type
    if ($is_electric && !empty($battery_type)) {
        $set_attr('battery-type', array(strtoupper($battery_type)));
    }

    // Battery Warranty
    if ($is_electric && !empty($battery_warranty)) {
        $set_attr('battery-warranty', array(strtoupper($battery_warranty)));
    }

    // Brush Guard (Denago/Evolution = YES, others = NO)
    $make_upper = strtoupper($make_symbol);
    $has_brush = ($make_upper === 'DENAGO®' || $make_upper === 'EVOLUTION®') ? 'YES' : 'NO';
    $set_attr('brush-guard', array($has_brush));

    // Drivetrain
    $set_attr('drivetrain', array(strtoupper($drive_train)));

    // Extended Top
    $set_attr('extended-top', array($has_extended_top ? 'YES' : 'NO'));

    // Lift Kit
    $set_attr('lift-kit', array($is_lifted ? '3 INCH' : 'NO'));

    // Location
    if (!empty($store_id)) {
        $store_data = DMS_API::get_city_and_state_by_store_id($store_id);
        $city  = $store_data['city'] ?? '';
        $state = $store_data['state'] ?? '';
        $loc_values = array();
        if (!empty($city) && !empty($state)) {
            $loc_values[] = strtoupper($city . ' ' . $state);
        }
        if (!empty($state)) {
            $loc_values[] = strtoupper($state);
        }
        if (!empty($loc_values)) {
            $set_attr('location', $loc_values);
        }
    }

    // Make-specific color attributes (or generic fallback)
    $make_color_attrs = array(
        'bintelli', 'club-car', 'denago', 'epic', 'evolution',
        'ezgo', 'icon', 'navitas', 'polaris', 'royal-ev',
        'star-ev', 'swift', 'tara', 'teko', 'tomberlin', 'yamaha',
    );
    if (in_array($make_lower, $make_color_attrs)) {
        if (!empty($color)) {
            $set_attr($make_lower . '-cart-colors', array(strtoupper($color)));
        }
        if (!empty($seat_color)) {
            $set_attr($make_lower . '-seat-colors', array(strtoupper($seat_color)));
        }
    } else {
        if (!empty($color)) {
            $set_attr('cart-color', array(strtoupper($color)));
        }
        if (!empty($seat_color)) {
            $set_attr('seat-color', array(strtoupper($seat_color)));
        }
    }

    // Sound System (New products with hasSoundSystem only)
    if (!$is_used && $has_sound) {
        $set_attr('sound-system', array(strtoupper($make_symbol) . ' SOUND SYSTEM'));
    }

    // Passengers
    if (!empty($passengers)) {
        $num_seats = $has_utility ? '2' : explode(' ', $passengers)[0];
        $set_attr('passengers', array($num_seats . ' SEATER'));
    }

    // Receiver Hitch
    $set_attr('receiver-hitch', array($has_hitch ? 'YES' : 'NO'));

    // Return Policy (New = Yes, Used = 90 Day)
    $set_attr('return-policy', array($is_used ? '90 DAY' : 'YES'));

    // Rim Size
    if (!empty($rim_size)) {
        $set_attr('rim-size', array($rim_size . ' INCH'));
    }

    // Shipping
    $set_attr('shipping', array('1 TO 3 DAYS LOCAL', '3 TO 7 DAYS OTR', '5 TO 9 DAYS NATIONAL'));

    // Side Step
    $side_step = $cart_data['cartAttributes']['sideStep'] ?? null;
    if (!empty($side_step)) {
        $side_step_terms = array();
        foreach ((array) $side_step as $step_val) {
            if (!empty($step_val)) {
                $side_step_terms[] = strtoupper($step_val);
            }
        }
        if (!empty($side_step_terms)) {
            $set_attr('side-step', $side_step_terms);
        }
    }

    // Street Legal
    $set_attr('street-legal', array($is_street_legal ? 'YES' : 'NO'));

    // Tire Profile
    if (!empty($tire_type)) {
        $tire_profile = strtoupper(str_replace('-', ' ', $tire_type));
        $set_attr('tire-profile', array($tire_profile));
    }

    // Utility Bed
    $has_bed = $cart_data['cartAttributes']['hasBed'] ?? '';
    if (!empty($has_bed) && is_string($has_bed)) {
        $set_attr('utility-bed', array(strtoupper($has_bed)));
    }

    // Store Code
    if (!empty($store_id)) {
        if (strtolower($store_id) === 'national') {
            $store_code_val = 'UNITED STATES OF AMERICA';
        } else {
            $loc_data = \Tigon\DmsConnect\Admin\Attributes::get_location($store_id);
            if ($loc_data) {
                $city = $loc_data['city_short'] ?? $loc_data['city'];
                $st = $loc_data['st'] ?? '';
                $store_code_val = strtoupper(trim($city . ' ' . $st));
            } else {
                $store_code_val = '';
            }
        }
        if (!empty($store_code_val)) {
            $set_attr('store-code', array($store_code_val));
        }
    }

    // Vehicle Class
    $vehicle_classes = array('GOLF CART', 'PERSONAL TRANSPORTATION VEHICLES (PTVS)');
    if ($is_street_legal) {
        $vehicle_classes[] = 'LOW SPEED VEHICLE (LSVS)';
        $vehicle_classes[] = 'MEDIUM SPEED VEHICLE (MSVS)';
    }
    if ($is_electric) {
        $vehicle_classes[] = 'NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)';
        $vehicle_classes[] = 'ZERO EMISSION VEHICLES (ZEVS)';
    }
    $has_bed = $cart_data['cartAttributes']['hasBed'] ?? false;
    if (!empty($has_bed)) {
        $vehicle_classes[] = 'UTILITY TASK VEHICLE (UTVS)';
    }
    if (empty($is_lifted)) {
        $vehicle_classes[] = 'NON-LIFTED';
    }
    $set_attr('vehicle-class', $vehicle_classes);

    // Vehicle MSRP
    $retail_price = $cart_data['retailPrice'] ?? 0;
    if (!empty($retail_price)) {
        $msrp_label = '$' . number_format((float) $retail_price, 0, '.', ',');
        $set_attr('vehicle-msrp', array(strtoupper($msrp_label)));
    }

    // Vehicle Power
    $power_value = $is_electric ? 'ELECTRIC' : 'GAS';
    $set_attr('vehicle-power', array($power_value));

    // Vehicle Status
    $status_value = $is_used ? 'USED' : 'NEW';
    $set_attr('vehicle-status', array($status_value));

    // Vehicle Warranty
    if (!empty($warranty)) {
        $set_attr('vehicle-warranty', array(strtoupper($warranty)));
    }

    // VIN
    $vin_no = $cart_data['vinNo'] ?? '';
    if (!empty($vin_no)) {
        $set_attr('vin', array(strtoupper($vin_no)));
    }

    // Year of Vehicle
    if (!empty($year)) {
        $set_attr('year-of-vehicle', array(strtoupper($year)));
    }

    // Save product attributes meta
    if (!empty($product_attributes)) {
        update_post_meta($product_id, '_product_attributes', $product_attributes);
    }
}

/**
 * Assign custom taxonomies mirroring Abstract_Cart::attach_taxonomies()
 *
 * @param int   $product_id WooCommerce product ID
 * @param array $cart_data  Full DMS cart payload
 */
function tigon_dms_assign_custom_taxonomies($product_id, $cart_data) {
    $make  = $cart_data['cartType']['make'] ?? '';
    $model = $cart_data['cartType']['model'] ?? '';
    $is_used       = !empty($cart_data['isUsed']);
    $is_electric   = !empty($cart_data['isElectric']);
    $is_street_legal = !empty($cart_data['title']['isStreetLegal']);
    $is_lifted     = !empty($cart_data['cartAttributes']['isLifted']);
    $has_utility   = ($cart_data['cartAttributes']['passengers'] ?? '') === 'Utility';
    $drive_train   = $cart_data['cartAttributes']['driveTrain'] ?? '2X4';
    $has_hitch     = !empty($cart_data['cartAttributes']['hitch']);
    $has_sound     = !empty($cart_data['cartAttributes']['hasSoundSystem']);
    $store_id      = $cart_data['cartLocation']['locationId'] ?? '';
    $is_rental     = !empty($cart_data['isRental']);

    $make_symbol = tigon_dms_get_make_with_symbol($make);

    // Cached Attributes instance for fast taxonomy term lookups
    $attrs = tigon_dms_get_attributes_instance();

    // Map taxonomy slugs to Attributes property names for O(1) lookups
    $taxonomy_map = array(
        'manufacturers'    => 'manufacturers_taxonomy',
        'models'           => 'models_taxonomy',
        'sound-systems'    => 'sound_systems_taxonomy',
        'added-features'   => 'added_features_taxonomy',
        'vehicle-class'    => 'vehicle_classes_taxonomy',
        'inventory-status' => 'inventory_status_taxonomy',
        'drivetrain'       => 'drivetrains_taxonomy',
        'rims'             => 'rims_taxonomy',
    );

    // Helper: safely assign terms using Attributes cache or DB fallback
    $assign = function($taxonomy, $term_names) use ($product_id, $attrs, $taxonomy_map) {
        if (!taxonomy_exists($taxonomy)) {
            return;
        }
        $ids = array();
        foreach ((array) $term_names as $name) {
            if (empty($name)) continue;

            // Fast path: use Attributes pre-loaded taxonomy map
            $prop = $taxonomy_map[$taxonomy] ?? null;
            if ($attrs && $prop && isset($attrs->$prop[strtoupper($name)])) {
                $ids[] = (int) $attrs->$prop[strtoupper($name)];
                continue;
            }

            // Slow path: individual DB lookup
            $term = get_term_by('name', $name, $taxonomy);
            if (!$term) {
                $term = get_term_by('slug', sanitize_title($name), $taxonomy);
            }
            if ($term && !is_wp_error($term)) {
                $ids[] = (int) $term->term_id;
            }
        }
        if (!empty($ids)) {
            wp_set_object_terms($product_id, $ids, $taxonomy, true);
        }
    };

    // Manufacturers taxonomy (with special case handling)
    $mfg_name = strtoupper($make_symbol);
    if ($mfg_name === 'SWIFT EV®') {
        $mfg_name = 'SWIFT®';
    } elseif ($mfg_name === 'STAR®') {
        $mfg_name = 'STAR EV®';
    }
    $assign('manufacturers', array($mfg_name));

    // Models taxonomy (with special case handling from Abstract_Cart)
    $model_upper = strtoupper($model);
    if ($model_upper === 'DS') {
        $model_name = strtoupper($make_symbol) . ' DS ELECTRIC';
    } elseif ($model_upper === 'PRECEDENT') {
        $model_name = strtoupper($make_symbol) . ' PRECEDENT ELECTRIC';
    } elseif ($model_upper === '4L') {
        $model_name = strtoupper($make_symbol) . ' CROWN 4 LIFTED';
    } elseif ($model_upper === '6L') {
        $model_name = strtoupper($make_symbol) . ' CROWN 6 LIFTED';
    } elseif ($model_upper === 'DRIVE 2') {
        $model_name = strtoupper($make_symbol) . ' DRIVE2';
    } elseif (strtoupper($make_symbol) === 'STAR EV®') {
        $model_name = 'STAR EV® ' . $model_upper;
    } elseif (strtoupper($make_symbol) === 'EZGO®') {
        $model_name = 'EZ-GO® ' . $model_upper;
    } else {
        $model_name = strtoupper($make_symbol . ' ' . $model);
    }
    $assign('models', array($model_name));

    // Sound Systems taxonomy — based on hasSoundSystem + optional soundSystem override
    if ($has_sound) {
        $custom_sound = $cart_data['addons']['soundSystem'] ?? null;
        if (!empty($custom_sound) && is_string($custom_sound)) {
            $sound_name = strtoupper($custom_sound);
        } elseif (strtoupper($make_symbol) === 'SWIFT®') {
            $sound_name = 'SWIFT EV® SOUND SYSTEM';
        } else {
            $sound_name = strtoupper($make_symbol) . ' SOUND SYSTEM';
        }
        // Try cached lookup first, fall back to auto-create
        $ss_prop = $attrs->sound_systems_taxonomy ?? array();
        if (isset($ss_prop[$sound_name])) {
            wp_set_object_terms($product_id, array((int) $ss_prop[$sound_name]), 'sound-systems', true);
        } else {
            $existing = get_term_by('name', $sound_name, 'sound-systems');
            if (!$existing) {
                $existing = get_term_by('slug', sanitize_title($sound_name), 'sound-systems');
            }
            if ($existing && !is_wp_error($existing)) {
                wp_set_object_terms($product_id, array((int) $existing->term_id), 'sound-systems', true);
            } else {
                $new_ss = wp_insert_term($sound_name, 'sound-systems', array('slug' => sanitize_title($sound_name)));
                if (!is_wp_error($new_ss)) {
                    wp_set_object_terms($product_id, array((int) $new_ss['term_id']), 'sound-systems', true);
                }
            }
        }
    } else {
        // No sound system
        $assign('sound-systems', array('NO SOUND SYSTEM'));
    }

    // Rims taxonomy — based on tireRimSize (e.g. "14" becomes "14 INCH")
    if (!empty($rim_size)) {
        $rim_name = strtoupper($rim_size . ' INCH');
        $rims_prop = $attrs->rims_taxonomy ?? array();
        if (isset($rims_prop[$rim_name])) {
            wp_set_object_terms($product_id, array((int) $rims_prop[$rim_name]), 'rims', true);
        } else {
            $existing_rim = get_term_by('name', $rim_name, 'rims');
            if (!$existing_rim) {
                $existing_rim = get_term_by('slug', sanitize_title($rim_name), 'rims');
            }
            if ($existing_rim && !is_wp_error($existing_rim)) {
                wp_set_object_terms($product_id, array((int) $existing_rim->term_id), 'rims', true);
            } else {
                $new_rim = wp_insert_term($rim_name, 'rims', array('slug' => sanitize_title($rim_name)));
                if (!is_wp_error($new_rim)) {
                    wp_set_object_terms($product_id, array((int) $new_rim['term_id']), 'rims', true);
                }
            }
        }
    }

    // Added Features taxonomy
    $added_features = array();
    if (isset($cart_data['addedFeatures'])) {
        $af = $cart_data['addedFeatures'];
        if (!empty($af['staticStock']))  $added_features[] = 'STATIC STOCK';
        if (!empty($af['brushGuard']))   $added_features[] = 'BRUSH GUARD';
        if (!empty($af['clayBasket']))   $added_features[] = 'CLAY BASKET';
        if (!empty($af['fenderFlares'])) $added_features[] = 'FENDER FLARES';
        if (!empty($af['LEDs']))         $added_features[] = 'LEDS';
        if (!empty($af['lightBar']))     $added_features[] = 'LIGHT BAR';
        if (!empty($af['underGlow']))    $added_features[] = 'UNDER GLOW';
        if (!empty($af['stockOptions'])) $added_features[] = 'STOCK OPTIONS';
    }
    if ($is_lifted) {
        $added_features[] = 'LIFT KIT';
    }
    if ($has_hitch) {
        $added_features[] = 'TOW HITCH';
    }
    if (!empty($added_features)) {
        $assign('added-features', $added_features);
    }

    // Vehicle Class taxonomy
    $vc = array('GOLF CART');
    if ($is_electric) {
        $vc[] = 'ZERO EMISSION VEHICLES (ZEVS)';
        if ($is_street_legal) {
            $vc[] = 'LOW SPEED VEHICLE (LSVS)';
            $vc[] = 'MEDIUM SPEED VEHICLE (MSVS)';
            $vc[] = 'NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)';
        }
    }
    if ($is_street_legal) {
        $vc[] = 'PERSONAL TRANSPORTATION VEHICLES (PTVS)';
    }
    if ($has_utility) {
        $vc[] = 'UTILITY TASK VEHICLE (UTVS)';
    }
    $assign('vehicle-class', $vc);

    // Inventory Status taxonomy (includes rental support)
    if ($is_rental) {
        $inv_status = $is_used ? 'LOCAL USED RENTAL INVENTORY' : 'LOCAL NEW RENTAL INVENTORY';
    } else {
        $inv_status = $is_used ? 'LOCAL USED ACTIVE INVENTORY' : 'LOCAL NEW ACTIVE INVENTORY';
    }
    $assign('inventory-status', array($inv_status));

    // Drivetrain taxonomy
    $assign('drivetrain', array(strtoupper($drive_train)));

    // Location taxonomy (city and state term IDs from Attributes)
    if (!empty($store_id) && class_exists('\Tigon\DmsConnect\Admin\Attributes')) {
        $loc = \Tigon\DmsConnect\Admin\Attributes::$locations[$store_id] ?? null;
        if ($loc) {
            $location_ids = array();
            if (!empty($loc['city_id'])) {
                $location_ids[] = (int) $loc['city_id'];
            }
            if (!empty($loc['state_id'])) {
                $location_ids[] = (int) $loc['state_id'];
            }
            if (!empty($location_ids) && taxonomy_exists('location')) {
                wp_set_object_terms($product_id, $location_ids, 'location', true);
            }
        }
    }
}

/**
 * Set Yoast SEO meta mirroring Abstract_Cart::generate_descriptions()
 *
 * @param int   $product_id WooCommerce product ID
 * @param array $cart_data  Full DMS cart payload
 */
function tigon_dms_set_seo_meta($product_id, $cart_data) {
    $make  = $cart_data['cartType']['make'] ?? '';
    $model = $cart_data['cartType']['model'] ?? '';
    $color = $cart_data['cartAttributes']['cartColor'] ?? '';
    $store_id = $cart_data['cartLocation']['locationId'] ?? '';

    $make_symbol = tigon_dms_get_make_with_symbol($make);
    $make_model_color = trim($make_symbol . ' ' . $model . ' ' . $color);

    // Build TIGON location text and phone number
    $tigon_text = 'TIGON Golf Carts';
    $phone = '';
    if (!empty($store_id)) {
        $store_data = DMS_API::get_city_and_state_by_store_id($store_id);
        $city  = $store_data['city'] ?? '';
        $state = $store_data['state'] ?? '';
        if (!empty($city) && !empty($state)) {
            $tigon_text = 'TIGON® Golf Carts ' . $city . ', ' . $state;
        }
        // Try to get phone from Attributes static locations
        if (class_exists('\Tigon\DmsConnect\Admin\Attributes')) {
            $loc = \Tigon\DmsConnect\Admin\Attributes::$locations[$store_id] ?? null;
            if ($loc && !empty($loc['phone'])) {
                $phone = $loc['phone'];
            }
        }
    }

    // Meta description (matches Abstract_Cart pattern)
    $meta_desc = $make_model_color . ' At ' . $tigon_text . '.';
    if (!empty($phone)) {
        $meta_desc .= ' Call Now ' . $phone . ' Get 0% Financing, and Shipping Options Today!';
    } else {
        $meta_desc .= ' Get 0% Financing, and Shipping Options Today!';
    }

    // SEO title
    $seo_title = $make_model_color . ' | ' . $tigon_text;

    // Set Yoast SEO meta (only if Yoast is active)
    if (defined('WPSEO_VERSION') || class_exists('WPSEO_Meta')) {
        update_post_meta($product_id, '_yoast_wpseo_title', sanitize_text_field($seo_title));
        update_post_meta($product_id, '_yoast_wpseo_metadesc', sanitize_text_field($meta_desc));

        // Product name for focus keywords and breadcrumb (mirrors Database_Object)
        $product_name = get_the_title($product_id);
        if (!empty($product_name)) {
            update_post_meta($product_id, '_yoast_wpseo_focus_kw', sanitize_text_field($product_name));
            update_post_meta($product_id, '_yoast_wpseo_focus_keywords', sanitize_text_field($product_name));
            update_post_meta($product_id, '_yoast_wpseo_bctitle', sanitize_text_field($product_name));
        }

        // OpenGraph title + description (mirrors Database_Object)
        update_post_meta($product_id, '_yoast_wpseo_opengraph-title', sanitize_text_field($product_name ?: $seo_title));
        update_post_meta($product_id, '_yoast_wpseo_opengraph-description', sanitize_text_field($meta_desc));

        // OpenGraph + Twitter images from featured image (mirrors Database_Object)
        $featured_id = get_post_thumbnail_id($product_id);
        if (!empty($featured_id)) {
            $featured_url = wp_get_attachment_image_url($featured_id, 'full');
            update_post_meta($product_id, '_yoast_wpseo_opengraph-image-id', $featured_id);
            if ($featured_url) {
                update_post_meta($product_id, '_yoast_wpseo_opengraph-image', esc_url_raw($featured_url));
            }
            update_post_meta($product_id, '_yoast_wpseo_twitter-image-id', $featured_id);
            if ($featured_url) {
                update_post_meta($product_id, '_yoast_wpseo_twitter-image', esc_url_raw($featured_url));
            }
        }
    }
}

/**
 * Set Product_Fields meta that mirrors Database_Object field writing.
 *
 * Covers: stock, tax, Google/Facebook extras, Yoast primary terms,
 * cornerstone, TIGON watermark, monroney sticker, WCPA, shipping class,
 * custom tabs, and custom product options.
 *
 * @param int   $product_id WooCommerce product ID
 * @param array $cart_data  Full DMS cart payload
 */
function tigon_dms_set_product_fields_meta($product_id, $cart_data) {
    $make  = $cart_data['cartType']['make'] ?? '';
    $model = $cart_data['cartType']['model'] ?? '';
    $color = $cart_data['cartAttributes']['cartColor'] ?? '';
    $store_id  = $cart_data['cartLocation']['locationId'] ?? '';
    $is_used   = !empty($cart_data['isUsed']);

    $make_symbol = tigon_dms_get_make_with_symbol($make);

    // ---------------------------------------------------------------
    // 1. Stock & Tax (from set_simple_fields)
    // ---------------------------------------------------------------
    update_post_meta($product_id, '_stock', 10000);
    update_post_meta($product_id, '_tax_status', 'taxable');
    update_post_meta($product_id, '_tax_class', 'standard');

    // ---------------------------------------------------------------
    // 2. Google Shopping extras (gender, adult, age_group)
    // ---------------------------------------------------------------
    update_post_meta($product_id, '_wc_gla_gender', 'unisex');
    update_post_meta($product_id, '_wc_gla_adult', 'no');
    update_post_meta($product_id, '_wc_gla_age_group', 'all ages');

    // Google category (also set by create function, ensure consistency)
    update_post_meta($product_id, '_wc_gla_google_product_category', 'Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts');

    // ---------------------------------------------------------------
    // 3. Facebook extras (gender, age_group)
    // ---------------------------------------------------------------
    update_post_meta($product_id, '_wc_facebook_enhanced_catalog_attributes_gender', 'unisex');
    update_post_meta($product_id, '_wc_facebook_enhanced_catalog_attributes_age_group', 'all ages');

    // ---------------------------------------------------------------
    // 4. Yoast primary terms + cornerstone
    // ---------------------------------------------------------------
    $yoast_active = defined('WPSEO_VERSION') || class_exists('WPSEO_Meta');

    if ($yoast_active) {
        // Primary category = make category (use Attributes cache for O(1) lookup)
        if (!empty($make)) {
            $attrs = tigon_dms_get_attributes_instance();
            $make_cat_id = null;
            if ($attrs && !empty($attrs->categories)) {
                $make_cat_id = $attrs->categories[strtoupper($make_symbol)] ?? null;
            }
            if (!$make_cat_id) {
                $make_cat_id = tigon_dms_get_existing_category($make, 0);
            }
            if ($make_cat_id) {
                update_post_meta($product_id, '_yoast_wpseo_primary_product_cat', $make_cat_id);
            }
        }

        // Primary location = city term ID from Attributes
        if (!empty($store_id) && class_exists('\Tigon\DmsConnect\Admin\Attributes')) {
            $loc = \Tigon\DmsConnect\Admin\Attributes::$locations[$store_id] ?? null;
            if ($loc && !empty($loc['city_id'])) {
                update_post_meta($product_id, '_yoast_wpseo_primary_location', $loc['city_id']);
            }
        }

        // Primary model = model taxonomy term ID
        if (!empty($model) && taxonomy_exists('models')) {
            $model_upper = strtoupper($model);
            if ($model_upper === 'DS') {
                $model_term_name = strtoupper($make_symbol) . ' DS ELECTRIC';
            } elseif ($model_upper === 'PRECEDENT') {
                $model_term_name = strtoupper($make_symbol) . ' PRECEDENT ELECTRIC';
            } elseif ($model_upper === '4L') {
                $model_term_name = strtoupper($make_symbol) . ' CROWN 4 LIFTED';
            } elseif ($model_upper === '6L') {
                $model_term_name = strtoupper($make_symbol) . ' CROWN 6 LIFTED';
            } elseif ($model_upper === 'DRIVE 2') {
                $model_term_name = strtoupper($make_symbol) . ' DRIVE2';
            } elseif (strtoupper($make_symbol) === 'STAR EV®') {
                $model_term_name = 'STAR EV® ' . $model_upper;
            } elseif (strtoupper($make_symbol) === 'EZGO®') {
                $model_term_name = 'EZ-GO® ' . $model_upper;
            } else {
                $model_term_name = strtoupper($make_symbol . ' ' . $model);
            }

            // Fast path: Attributes cache
            $model_term_id = null;
            if ($attrs && !empty($attrs->models_taxonomy)) {
                $model_term_id = $attrs->models_taxonomy[strtoupper($model_term_name)] ?? null;
            }
            if (!$model_term_id) {
                // Slow path: DB lookup
                $model_term = get_term_by('name', $model_term_name, 'models');
                if (!$model_term) {
                    $model_term = get_term_by('slug', sanitize_title($model_term_name), 'models');
                }
                if ($model_term && !is_wp_error($model_term)) {
                    $model_term_id = $model_term->term_id;
                }
            }
            if ($model_term_id) {
                update_post_meta($product_id, '_yoast_wpseo_primary_models', $model_term_id);
            }
        }

        // Primary added-feature (mirrors Database_Object primary_added_feature)
        if (taxonomy_exists('added-features')) {
            // Use the first added-feature from the cart as primary
            $added_features = $cart_data['cartAttributes']['addedFeatures'] ?? [];
            if (!empty($added_features) && is_array($added_features)) {
                $first_feature = is_string($added_features[0]) ? $added_features[0] : ($added_features[0]['name'] ?? '');
                if (!empty($first_feature)) {
                    $feature_upper = strtoupper($first_feature);
                    $feature_term_id = null;
                    if ($attrs && !empty($attrs->added_features_taxonomy)) {
                        $feature_term_id = $attrs->added_features_taxonomy[$feature_upper] ?? null;
                    }
                    if (!$feature_term_id) {
                        $feature_term = get_term_by('name', $first_feature, 'added-features');
                        if ($feature_term && !is_wp_error($feature_term)) {
                            $feature_term_id = $feature_term->term_id;
                        }
                    }
                    if ($feature_term_id) {
                        update_post_meta($product_id, '_yoast_wpseo_primary_added-features', $feature_term_id);
                    }
                }
            }
        }

        // Cornerstone content
        update_post_meta($product_id, '_yoast_wpseo_is_cornerstone', '1');
    }

    // ---------------------------------------------------------------
    // 5. TIGON watermark text
    // ---------------------------------------------------------------
    $tigonwm = 'TIGON®';
    if (!empty($store_id)) {
        if (class_exists('\Tigon\DmsConnect\Admin\Attributes')) {
            $loc = \Tigon\DmsConnect\Admin\Attributes::$locations[$store_id] ?? null;
            if ($loc) {
                $tigonwm = 'TIGON® Golf Carts ' . ($loc['city'] ?? '') . ', ' . ($loc['state'] ?? '');
            }
        } else {
            $store_data = DMS_API::get_city_and_state_by_store_id($store_id);
            $city  = $store_data['city'] ?? '';
            $state = $store_data['state'] ?? '';
            if (!empty($city) && !empty($state)) {
                $tigonwm = 'TIGON® Golf Carts ' . $city . ', ' . $state;
            }
        }
    }
    update_post_meta($product_id, '_tigonwm', sanitize_text_field($tigonwm));

    // ---------------------------------------------------------------
    // 6. Monroney sticker (window sticker PDF)
    // ---------------------------------------------------------------
    $sticker_url = $cart_data['cartWindowStickerUrl'] ?? '';
    if (!empty($sticker_url)) {
        update_post_meta($product_id, 'monroney_sticker', esc_url_raw($sticker_url));
        // ACF field container ID (from Abstract_Cart)
        update_post_meta($product_id, '_monroney_sticker', 'field_66e3332abf481');
    }

    // ---------------------------------------------------------------
    // 7. WCPA - Exclude global addon forms
    // ---------------------------------------------------------------
    update_post_meta($product_id, 'wcpa_exclude_global_forms', '1');

    // ---------------------------------------------------------------
    // 8. Shipping class term
    // ---------------------------------------------------------------
    if (taxonomy_exists('product_shipping_class')) {
        // Rentals get "Golf Cart Rentals", everything else gets "Golf Cart Shipping"
        if ($is_rental) {
            $shipping_slug = 'golf-cart-rentals';
            $shipping_name = 'Golf Cart Rentals';
        } else {
            $shipping_slug = 'golf-cart-shipping';
            $shipping_name = 'Golf Cart Shipping';
        }
        $shipping_term = get_term_by('slug', $shipping_slug, 'product_shipping_class');
        if (!$shipping_term || is_wp_error($shipping_term)) {
            $new_shipping = wp_insert_term($shipping_name, 'product_shipping_class', array('slug' => $shipping_slug));
            if (!is_wp_error($new_shipping)) {
                wp_set_object_terms($product_id, array($new_shipping['term_id']), 'product_shipping_class');
            }
        } else {
            wp_set_object_terms($product_id, array($shipping_term->term_id), 'product_shipping_class');
        }
    }

    // ---------------------------------------------------------------
    // 9. Custom product tabs (Yikes WooCommerce Product Tabs)
    // ---------------------------------------------------------------
    $tabs_meta = tigon_dms_build_custom_tabs($cart_data);
    if (!empty($tabs_meta)) {
        update_post_meta($product_id, '_yikes_woo_products_tabs', $tabs_meta);
    }

    // ---------------------------------------------------------------
    // 10. Custom product options (WCPA addon forms)
    // ---------------------------------------------------------------
    $options_meta = tigon_dms_build_custom_options($cart_data);
    if (!empty($options_meta)) {
        update_post_meta($product_id, '_wcpa_product_meta', $options_meta);
    }
}

/**
 * Build custom product tabs for the Yikes WooCommerce Product Tabs plugin.
 *
 * Mirrors Abstract_Cart::attach_custom_tabs() — assigns warranty, specs,
 * video, and manual tabs based on make and model.
 *
 * @param array $cart_data Full DMS cart payload
 * @return string|null Serialized tabs array or null
 */
function tigon_dms_build_custom_tabs($cart_data) {
    // Use Attributes cache for pre-loaded tabs (already indexed by name)
    $attrs = tigon_dms_get_attributes_instance();
    if ($attrs && !empty($attrs->tabs)) {
        $tabs_by_name = $attrs->tabs;
    } else {
        // Fallback: load tabs from Yikes option
        $saved_tabs = get_option('yikes_woo_reusable_products_tabs');
        if (empty($saved_tabs) || !is_array($saved_tabs)) {
            return null;
        }
        $tabs_by_name = array();
        foreach ($saved_tabs as $tab) {
            if (!empty($tab['tab_name'])) {
                $tabs_by_name[$tab['tab_name']] = $tab;
            }
        }
    }

    $make   = $cart_data['cartType']['make'] ?? '';
    $model  = $cart_data['cartType']['model'] ?? '';
    $year   = $cart_data['cartType']['year'] ?? '';
    $is_used = !empty($cart_data['isUsed']);

    $make_symbol = tigon_dms_get_make_with_symbol($make);
    $make_upper  = strtoupper($make_symbol);

    $tab_names = array();

    // Used cart warranty
    if ($is_used) {
        $tab_names[] = 'TIGON Warranty (USED GOLF CARTS)';
    }

    // Make/model-specific tabs (from Abstract_Cart::attach_custom_tabs)
    switch ($make_upper) {
        case 'DENAGO®':
            if (!$is_used) {
                $tab_names[] = 'DENAGO Warranty';
            }
            if ($year === '2024') {
                $tab_names[] = 'VIDEO DENAGO 2024';
            }
            switch (strtoupper($model)) {
                case 'NOMAD':
                    $tab_names[] = 'Denago® Nomad Vehicle Specs';
                    break;
                case 'NOMAD XL':
                    $tab_names[] = 'Denago® Nomad XL Vehicle Specs';
                    $tab_names[] = 'Denago Nomad XL User Manual';
                    if ($year === '2024') $tab_names[] = 'PICS DENAGO NOMAD XL 2024';
                    break;
                case 'ROVER XL':
                    $tab_names[] = 'Denago® Rover XL Vehicle Specs';
                    if ($year === '2024') $tab_names[] = 'PICS DENAGO ROVER XL 2024';
                    break;
            }
            break;

        case 'EVOLUTION®':
            if (!$is_used) {
                $tab_names[] = 'EVolution Warranty';
            }
            switch (strtoupper($model)) {
                case 'CLASSIC 2 PRO':
                    $tab_names[] = 'EVolution Classic 2 Pro Images';
                    $tab_names[] = 'EVolution Classic 2 Pro Specs';
                    break;
                case 'CLASSIC 2 PLUS':
                    $tab_names[] = 'EVolution Classic 2 Plus Images';
                    $tab_names[] = 'EVolution Classic 2 Plus Specs';
                    break;
                case 'CLASSIC 4 PRO':
                    $tab_names[] = 'EVolution Classic 4 Pro Images';
                    $tab_names[] = 'EVolution Classic 4 Pro Specs';
                    break;
                case 'CLASSIC 4 PLUS':
                    $tab_names[] = 'EVolution Classic 4 Plus Images';
                    $tab_names[] = 'EVolution Classic 4 Plus Specs';
                    break;
                case 'D5 MAVERICK 2+2':
                    $tab_names[] = 'EVolution D5-Maverick 2+2';
                    $tab_names[] = 'EVolution D5-Maverick 2+2 Images';
                    break;
                case 'D5 MAVERICK 2+2 PLUS':
                    $tab_names[] = 'EVolution D5-Maverick 2+2 Plus Images';
                    break;
                case 'D5 RANGER 2+2':
                    $tab_names[] = 'EVOLUTION D5 RANGER 2+2 IMAGES';
                    $tab_names[] = 'EVOLUTION D5 RANGER 2+2 SPECS';
                    break;
                case 'D5 RANGER 2+2 PLUS':
                    $tab_names[] = 'EVOLUTION D5 RANGER 2+2 PLUS IMAGES';
                    $tab_names[] = 'EVOLUTION D5 RANGER 2+2 PLUS SPECS';
                    break;
                case 'D5 RANGER 4':
                    $tab_names[] = 'EVOLUTION D5 RANGER 4 IMAGES';
                    $tab_names[] = 'EVOLUTION D5 RANGER 4 SPEC';
                    break;
                case 'D5 RANGER 4 PLUS':
                    $tab_names[] = 'EVOLUTION D5 RANGER 4 PLUS IMAGES';
                    $tab_names[] = 'EVOLUTION D5 RANGER 4 PLUS SPECS';
                    break;
                case 'D5 RANGER 6':
                    $tab_names[] = 'EVOLUTION D5 RANGER 6 IMAGES';
                    $tab_names[] = 'EVOLUTION D5 RANGER 6 SPECS';
                    break;
            }
            break;
    }

    if (empty($tab_names)) {
        return null;
    }

    // Build tab array from saved Yikes tabs
    $tabs = array();
    foreach ($tab_names as $name) {
        if (isset($tabs_by_name[$name])) {
            $tab = $tabs_by_name[$name];
            $tabs[] = array(
                'name'    => $name,
                'id'      => $tab['tab_id'] ?? '',
                'title'   => $tab['tab_title'] ?? $name,
                'content' => preg_replace('/\\\*(&quot;)*/', '', $tab['tab_content'] ?? ''),
            );
        }
    }

    return !empty($tabs) ? serialize($tabs) : null;
}

/**
 * Build custom product options (WCPA addon forms) per make/model.
 *
 * Mirrors Abstract_Cart::attach_custom_options() — assigns make/model-specific
 * addon forms for new carts, or individual addon matching for used carts.
 *
 * @param array $cart_data Full DMS cart payload
 * @return string|null Serialized options array or null
 */
function tigon_dms_build_custom_options($cart_data) {
    // Use Attributes cache for pre-loaded WCPA forms (already indexed by title)
    $attrs = tigon_dms_get_attributes_instance();
    if ($attrs && !empty($attrs->custom_options)) {
        $forms_by_title = $attrs->custom_options;
    } else {
        // Fallback: load WCPA forms from DB
        $forms = get_posts(array(
            'post_type'   => 'wcpa_pt_forms',
            'numberposts' => -1,
        ));
        if (empty($forms)) {
            return null;
        }
        $forms_by_title = array();
        foreach ($forms as $form) {
            $forms_by_title[$form->post_title] = $form->ID;
        }
    }

    $make   = $cart_data['cartType']['make'] ?? '';
    $model  = $cart_data['cartType']['model'] ?? '';
    $is_used = !empty($cart_data['isUsed']);
    $passengers = $cart_data['cartAttributes']['passengers'] ?? '';
    $num_seats  = ($passengers === 'Utility') ? '2' : (explode(' ', $passengers)[0] ?? '');

    $make_symbol = tigon_dms_get_make_with_symbol($make);
    $make_upper  = strtoupper($make_symbol);

    $option_ids = array();

    if (!$is_used) {
        // New carts: find the make/model-specific addon list
        if ($make_upper === 'DENAGO®') {
            $addon_list = 'Denago® EV ' . $model . ' Add Ons';
        } elseif ($make_upper === 'EPIC®') {
            $addon_list = 'EPIC® ' . $model . ' Add Ons';
        } elseif ($make_upper === 'EVOLUTION®') {
            $addon_list = 'EVolution® ' . $model . ' Add Ons';
            if (substr($model, 0, 3) === 'D5 ') {
                $m = $model;
                $pos = strpos($m, ' ');
                if ($pos !== false) {
                    $m = substr_replace($m, '-', $pos, 1);
                }
                $addon_list = 'EVolution® ' . $m . ' Add Ons';
            }
        } elseif ($make_upper === 'ICON®') {
            $addon_list = 'ICON® ' . $model . ' Add Ons';
        } elseif ($make_upper === 'SWIFT EV®') {
            $addon_list = 'SWIFT EV® ' . $model . ' Add Ons';
        } else {
            $addon_list = $make_symbol . ' ' . $model . ' Add Ons';
        }

        if (isset($forms_by_title[$addon_list])) {
            $option_ids[] = $forms_by_title[$addon_list];
        }
    } else {
        // Used carts: match individual addons from cart data
        $addons = array_flip($cart_data['advertising']['cartAddOns'] ?? array());
        $addon_map = array(
            'Golf cart enclosure 2 passenger 600' => array('form' => '2 Passenger Golf Cart Enclosure', 'seats' => '2'),
            'Golf cart enclosure 4 Passenger 800'  => array('form' => '4 Passenger Golf Cart Enclosure', 'seats' => '4'),
            'Golf cart enclosure 6 passenger 1200'  => array('form' => '6 Passenger Golf Cart Enclosure', 'seats' => '6'),
            '120 Volt inverter 500'                 => array('form' => '120 Volt Inverter'),
            '32 inch light bar 220'                 => array('form' => '32in LED Light Bar'),
            'Cargo caddie 250'                      => array('form' => 'Cargo Caddie'),
            'Rear seat cupholders 80'               => array('form' => 'Rear Seat Cupholders'),
            'Upgraded charger 210'                  => array('form' => 'Upgraded Charger'),
            'Breezeasy Fan System 400'              => array('form' => 'Breezeasy 3 Fan System'),
            'Golf bag attachment 120'               => array('form' => 'Golf Bag Attachment'),
            'Led light kit 350'                     => array('form' => 'LED Cart Light Kit'),
            'Led light kit with signals and horn 495' => array('form' => 'LED Cart Light Kit With Signals & Horn'),
            'Led under glow 400'                    => array('form' => 'LED Under Glow Lights'),
            'Led roof lights 400'                   => array('form' => 'LED Roof Lights'),
            'Rear seat kit 385'                     => array('form' => 'Rear Seat Kit'),
            'Basic 4 Passenger storage cover 150'   => array('form' => 'Basic 4 Passenger Storage Cover', 'seats' => '4'),
            'Premium 4 Passenger storage cover 300' => array('form' => 'Premium 4 Passenger Storage Cover', 'seats' => '4'),
            'Premium 6 Passenger storage cover 385' => array('form' => 'Premium 6 Passenger Storage Cover', 'seats' => '6'),
            '26 in sound bar 500'                   => array('form' => '26" Sound Bar'),
            '32 in Sound bar 600'                   => array('form' => '32" Sound Bar'),
            'EcoXGear subwoofer 745'                => array('form' => 'EcoXGear Subwoofer'),
            'New tinted windshield 210'             => array('form' => 'Tinted Windshield'),
            'Grab bar 85'                           => array('form' => 'Grab Bar'),
            'Deluxe Grab Bar 150'                   => array('form' => 'Deluxe Grab Bar'),
            'Side mirrors 65'                       => array('form' => 'Side Mirrors'),
            'Extended roof 500'                     => array('form' => 'Extended Roof 84"'),
        );

        // Hitch special case (3 tiers)
        if (isset($addons['Hitch 80']) && isset($addons['Hitch 300']) && isset($addons['Hitch 500'])) {
            foreach (array('Hitch Bolt On', 'Basic Hitch Weld On', 'Premium Hitch Weld On') as $hitch) {
                if (isset($forms_by_title[$hitch])) {
                    $option_ids[] = $forms_by_title[$hitch];
                }
            }
        }

        // Seat belt special case
        if (isset($addons['Seat belts 4 Passenger 160']) && $num_seats === '4') {
            if (isset($forms_by_title['Seat Belts 4 Passenger'])) {
                $option_ids[] = $forms_by_title['Seat Belts 4 Passenger'];
            }
        }
        if (isset($addons['Seat belts 6 Passenger 240']) && $num_seats === '6') {
            if (isset($forms_by_title['Seat Belts 6 Passenger'])) {
                $option_ids[] = $forms_by_title['Seat Belts 6 Passenger'];
            }
        }

        foreach ($addon_map as $addon_key => $config) {
            if (!isset($addons[$addon_key])) continue;
            // Check seat count restriction
            if (isset($config['seats']) && $config['seats'] !== $num_seats) continue;
            if (isset($forms_by_title[$config['form']])) {
                $option_ids[] = $forms_by_title[$config['form']];
            }
        }
    }

    return !empty($option_ids) ? serialize(array_unique($option_ids)) : null;
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
        'Location', 'Year of Vehicle',
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

// Sync page is now in Admin_Page::sync_page()

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
