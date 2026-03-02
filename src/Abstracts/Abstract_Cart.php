<?php

namespace Tigon\DmsConnect\Abstracts;

use Tigon\DmsConnect\Admin\Attributes;
use Tigon\DmsConnect\Admin\Database_Object;

use WP_Error;

abstract class Abstract_Cart
{
    protected $generated_attributes;

    protected static $defaults = [
        "cartType" => [
            "make" => 'Tigon',
            "model" => 'Golf Cart',
            "year" => null
        ],
        "cartAttributes" => [
            "cartColor" => null,
            "seatColor" => null,
            "tireRimSize" => null,
            "tireType" => null,
            "hasSoundSystem" => null,
            "isLifted" => null,
            "hasHitch" => null,
            "hasExtendedTop" => null,
            "passengers" => null,
            "utilityBed" => false
        ],
        "battery" => [
            "year" => null,
            "brand" => null,
            "type" => null,
            "serialNo" => null,
            "ampHours" => null,
            "batteryVoltage" => null,
            "packVoltage" => null,
            "warrantyLength" => null,
            "isDC" => null
        ],
        "engine" => [
            "make" => null,
            "horsepower" => null,
            "stroke" => '4'
        ],
        "cartLocation" => [
            "locationId" => 'T1',
            "locationDescription" => null
        ],
        "title" => [
            "isStreetLegal" => null,
            "isTitleInPossession" => null,
            "storeID" => null
        ],
        "rfsStatus" => [
            "isRFS" => null,
            "notRFSOption" => null,
            "notRFSDescription" => null
        ],
        "floorPlanned" => [
            "isFloorPlanned" => null,
            "floorPlannedTimestamp" => null
        ],
        "overheadCost" => [
            "cartCost" => null,
            "shippingCost" => null,
            "lsvCost" => null
        ],
        "advertising" => [
            "websiteUrl" => "default",
            "onWebsite" => null,
            "needOnWebsite" => false,
            "facebookAccounts" => null,
            "cartAddOns" => []
        ],
        "_id" => null,
        "lastInteractionTimestamp" => null,
        "retailPrice" => null,
        "salePrice" => null,
        "isElectric" => null,
        "transferLocation" => null,
        "serialNo" => null,
        "vinNo" => null,
        "inventoryTimestamp" => null,
        "serviceTimestamp" => null,
        "invoiceNo" => null,
        "currentOwner" => null,
        "tradeInInfo" => [],
        "isUsed" => null,
        "isOnLot" => null,
        "isDelivered" => null,
        "odometer" => null,
        "isService" => null,
        "isInBoneyard" => null,
        "isInStock" => null,
        "warrantyLength" => null,
        "isComplete" => null,
        "imageUrls" => [],
        "categories" => [],
        "internalCartImageUrls" => null,
        "pid" => null,
        "createdBy" => null,
        "creationTimestamp" => null,
        "__v" => null,
        "lastUpdateTimestamp" => null,
        "lastUpdatedBy" => null
    ];

    protected $cart;

    // Repeated-use variables
    protected $file_source;
    protected $already_exists;
    protected $make_with_symbol;
    protected $make_model_color;
    protected $location_id;
    protected $brand_hyphenated;
    protected $pattern_hyphenated;
    protected $color_hyphenated;
    protected $seat_color_hyphenated;
    protected $location_hyphenated;
    protected $number_seats;
    protected $city_shortname;

    // Utility import parameters
    protected $method;
    protected $product_id;

    // Import Parameters
    protected $name;
    protected $short_description;
    protected $description;
    protected $published;
    protected $comment_status;
    protected $ping_status;
    protected $menu_order;
    protected $post_type;
    protected $comment_count;
    protected $post_author;
    protected $slug;

    protected $sku;
    protected $tax_status;
    protected $tax_class;
    protected $manage_stock;
    protected $backorders_allowed;
    protected $sold_individually;
    protected $is_virtual;
    protected $downloadable;
    protected $download_limit;
    protected $download_expiry;
    protected $stock;
    protected $in_stock;
    protected $gui;
    protected $attributes;
    protected $images;
    protected $price;
    protected $sale_price;
    protected $yoast_seo_title;
    protected $meta_description;
    protected $primary_category;
    protected $primary_location;
    protected $primary_model;
    protected $primary_added_feature;
    protected $bit_is_cornerstone;
    protected $custom_tabs;
    protected $attr_exclude_global_forms;
    protected $custom_product_options;
    protected $condition;
    protected $google_brand;
    protected $google_color;
    protected $google_pattern;
    protected $gender;
    protected $google_size_system;
    protected $adult_content;
    protected $google_category;
    protected $age_group;
    protected $product_image_source;
    protected $facebook_sync;
    protected $facebook_visibility;

    protected $monroney_sticker;
    protected $monroney_container_id;
    protected $tigonwm_text;

    protected $taxonomy_terms;

    /**
     * Cached schema templates loaded from tigon_dms_config.
     *
     * @var array<string,string>|null
     */
    protected static $schema_templates = null;

    public function __construct($input_cart)
    {
        \Tigon\DmsConnect\Includes\Product_Fields::define_constants();
        $this->cart = array_filter($input_cart, function ($v) {
            return !($v === null) && !($v === "");
        });
        $this->cart = array_replace_recursive(self::$defaults, $this->cart);
        $this->generated_attributes = new Attributes();
    }

    /**
     * Load schema templates from tigon_dms_config, with sensible defaults.
     *
     * @return array<string,string>
     */
    protected function get_schema_templates(): array
    {
        if (self::$schema_templates !== null) {
            return self::$schema_templates;
        }

        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

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
            $value = $wpdb->get_var("SELECT option_value FROM $table_name WHERE option_name = '$key'");
            if ($value === null || $value === '') {
                $value = $default;
            }
            $templates[$key] = $value;
        }

        self::$schema_templates = $templates;
        return self::$schema_templates;
    }

    /**
     * Build a flat variable map for template substitution
     * from the current cart and location context.
     *
     * @return array<string,mixed>
     */
    protected function build_template_variables(): array
    {
        $loc = Attributes::$locations[$this->location_id] ?? [
            'city' => '',
            'st'   => '',
            'state'=> '',
        ];

        $vars = [
            'make'        => $this->cart['cartType']['make'] ?? '',
            'model'       => $this->cart['cartType']['model'] ?? '',
            'year'        => $this->cart['cartType']['year'] ?? '',
            'cartColor'   => $this->cart['cartAttributes']['cartColor'] ?? '',
            'seatColor'   => $this->cart['cartAttributes']['seatColor'] ?? '',
            'tireRimSize' => $this->cart['cartAttributes']['tireRimSize'] ?? '',
            'tireType'    => $this->cart['cartAttributes']['tireType'] ?? '',
            'passengers'  => $this->cart['cartAttributes']['passengers'] ?? '',

            'city'       => $loc['city'] ?? '',
            'state'      => $loc['state'] ?? '',
            'stateAbbr'  => $loc['st'] ?? '',
            'cityShort'  => $this->city_shortname ?? ($loc['city'] ?? ''),

            'retailPrice'=> $this->cart['retailPrice'] ?? '',
            'salePrice'  => $this->cart['salePrice'] ?? '',
        ];

        // Battery
        $vars['batteryType']      = $this->cart['battery']['type'] ?? '';
        $vars['batteryVoltage']   = $this->cart['battery']['batteryVoltage'] ?? '';
        $vars['packVoltage']      = $this->cart['battery']['packVoltage'] ?? '';
        $vars['batteryBrand']     = $this->cart['battery']['brand'] ?? '';
        $vars['batteryYear']      = $this->cart['battery']['year'] ?? '';
        $vars['batteryAmpHours']  = $this->cart['battery']['ampHours'] ?? '';
        $vars['batteryWarranty']  = $this->cart['battery']['warrantyLength'] ?? '';

        // Title / status
        $vars['isStreetLegal'] = isset($this->cart['title']['isStreetLegal'])
            ? ($this->cart['title']['isStreetLegal'] ? 'Yes' : 'No')
            : '';
        $vars['isElectric'] = isset($this->cart['isElectric'])
            ? ($this->cart['isElectric'] ? 'ELECTRIC' : 'GAS')
            : '';
        $vars['isUsed'] = isset($this->cart['isUsed'])
            ? ($this->cart['isUsed'] ? 'USED' : 'NEW')
            : '';

        // Identifiers
        $vars['serialNumber'] = $this->cart['serialNo'] ?? '';
        $vars['vinNumber']    = $this->cart['vinNo'] ?? '';

        return $vars;
    }

    /**
     * Evaluate a schema template string against provided variables.
     *
     * Supports {var} and {^var} (ucwords) placeholders.
     *
     * @param string $template
     * @param array<string,mixed> $vars
     * @param bool $slugify When true, returns a slugified version.
     * @return string
     */
    protected function evaluate_template(string $template, array $vars, bool $slugify = false): string
    {
        $result = preg_replace_callback('/\{([^}]+)\}/', function ($matches) use ($vars) {
            $key = $matches[1];
            $should_ucwords = false;

            if (str_starts_with($key, '^')) {
                $should_ucwords = true;
                $key = substr($key, 1);
            }

            $value = $vars[$key] ?? '';
            if (!is_string($value)) {
                $value = (string) $value;
            }

            if ($should_ucwords && $value !== '') {
                $value = ucwords(strtolower($value));
            }

            return $value;
        }, $template);

        // Normalize whitespace
        $result = trim(preg_replace('/\s+/', ' ', $result));

        if ($slugify) {
            $result = sanitize_title($result);
        }

        return $result;
    }

    public function convert(?int $fields = ALL_FIELDS)
    {
        $verified = $this->verify_data();
        if (is_wp_error($verified)) return $verified;

        global $wpdb;
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $table_name = $wpdb->prefix . 'tigon_dms_config';

        $this->file_source = $wpdb->get_var("SELECT option_value FROM $table_name WHERE option_name = 'file_source'");

        $this->get_pid();
        $this->generate_location_data();
        $this->create_name();
        $this->create_slug();
        $this->fetch_images();
        $this->fetch_monroney();
        $this->attach_categories_tags();
        $this->attach_attributes();
        $this->attach_taxonomies();
        $this->attach_custom_options();
        $this->attach_custom_tabs();
        $this->generate_descriptions();
        $this->set_simple_fields();

        $this->field_overrides();

        // Apply user-configured field mappings from the admin UI.
        // These can override any property set above.
        $this->apply_custom_field_mappings();

        return new Database_Object(
            method: $this->method,
            id: $this->product_id,
            name: $fields & NAME ?
                $this->name :
                null,
            short_description: $fields & SHORT_DESCRIPTION ?
                $this->short_description :
                null,
            description: $fields & DESCRIPTION ?
                $this->description :
                null,
            published: $fields & PUBLISHED ?
                $this->published :
                null,
            comment_status: $fields & COMMENT_STATUS ?
                $this->comment_status :
                null,
            ping_status: $fields & PING_STATUS ?
                $this->ping_status :
                null,
            menu_order: $fields & MENU_ORDER ?
                $this->menu_order :
                null,
            post_type: $fields & POST_TYPE ?
                $this->post_type :
                null,
            comment_count: $fields & COMMENT_COUNT ?
                $this->comment_count :
                null,
            post_author: $fields & POST_AUTHOR ?
                $this->post_author :
                null,
            slug: $fields & SLUG ?
                $this->slug :
                null,

            sku: $fields & SKU ?
                $this->sku :
                null,
            tax_status: $fields & TAX_STATUS ?
                $this->tax_status :
                null,
            tax_class: $fields & TAX_CLASS ?
                $this->tax_class :
                null,
            manage_stock: $fields & MANAGE_STOCK ?
                $this->manage_stock :
                null,
            backorders_allowed: $fields & BACKORDERS_ALLOWED ?
                $this->backorders_allowed :
                null,
            sold_individually: $fields & SOLD_INDIVIDUALLY ?
                $this->sold_individually :
                null,
            is_virtual: $fields & IS_VIRTUAL ?
                $this->is_virtual :
                null,
            downloadable: $fields & DOWNLOADABLE ?
                $this->downloadable :
                null,
            download_limit: $fields & DOWNLOAD_LIMIT ?
                $this->download_limit :
                null,
            download_expiry: $fields & DOWNLOAD_EXPIRY ?
                $this->download_expiry :
                null,
            stock: $fields & STOCK ?
                $this->stock :
                null,
            in_stock: $fields & IN_STOCK ?
                $this->in_stock :
                null,
            gui: $fields & GUI ?
                $this->gui :
                null,
            attributes: $fields & TAXONOMY_TERMS ?
                $this->attributes :
                null,
            images: $fields & IMAGES ?
                $this->images :
                null,
            price: $fields & PRICE ?
                $this->price :
                null,
            sale_price: $fields & SALE_PRICE ?
                $this->sale_price :
                null,

            yoast_seo_title: $fields & YOAST_SEO_TITLE ?
                $this->yoast_seo_title :
                null,
            meta_description: $fields & META_DESCRIPTION ?
                $this->meta_description :
                null,
            primary_category: $fields & PRIMARY_CATEGORY ?
                $this->primary_category :
                null,
            primary_location: $fields & PRIMARY_LOCATION ?
                $this->primary_location :
                null,
            primary_model: $fields & PRIMARY_MODEL ?
                $this->primary_model :
                null,
            primary_added_feature: $fields & PRIMARY_ADDED_FEATURE ?
                $this->primary_added_feature :
                null,
            bit_is_cornerstone: $fields & BIT_IS_CORNERSTONE ?
                $this->bit_is_cornerstone :
                null,

            custom_tabs: $fields & CUSTOM_TABS ?
                $this->custom_tabs :
                null,

            attr_exclude_global_forms: $fields & ATTR_EXCLUDE_GLOBAL_FORMS ?
                $this->attr_exclude_global_forms :
                null,
            custom_product_options: $fields & CUSTOM_PRODUCT_OPTIONS ?
                $this->custom_product_options :
                null,

            condition: $fields & CONDITION ?
                $this->condition :
                null,
            google_brand: $fields & GOOGLE_BRAND ?
                $this->google_brand :
                null,
            google_color: $fields & GOOGLE_COLOR ?
                $this->google_color :
                null,
            google_pattern: $fields & GOOGLE_PATTERN ?
                $this->google_pattern :
                null,
            gender: $fields & GENDER ?
                $this->gender :
                null,
            google_size_system: $fields & GOOGLE_SIZE_SYSTEM ?
                $this->google_size_system :
                null,
            adult_content: $fields & ADULT_CONTENT ?
                $this->adult_content :
                null,
            google_category: $fields & GOOGLE_CATEGORY ?
                $this->google_category :
                null,
            age_group: $fields & AGE_GROUP ?
                $this->age_group :
                null,
            product_image_source: $fields & PRODUCT_IMAGE_SOURCE ?
                $this->product_image_source :
                null,
            facebook_sync: $fields & FACEBOOK_SYNC ?
                $this->facebook_sync :
                null,
            facebook_visibility: $fields & FACEBOOK_VISIBILITY ?
                $this->facebook_visibility :
                null,

            monroney_sticker: $fields & MONRONEY_STICKER ?
                $this->monroney_sticker :
                null,
            monroney_container_id: $fields & MONRONEY_CONTAINER_ID ?
                $this->monroney_container_id :
                null,
            tigonwm_text: $fields & TIGONWM_TEXT ?
                $this->tigonwm_text :
                null,

            taxonomy_terms: $fields & TAXONOMY_TERMS ?
                $this->taxonomy_terms :
                null
        );
    }

    /**
     * Check if input cart is valid
     *
     * @return true|WP_Error
     */
    protected function verify_data()
    {
        throw new WP_Error('This function has not been implemented.');
    }

    /**
     * Initialize product id
     * Define already_exists
     *
     * @return void
     */
    protected function get_pid()
    {
        if (wc_get_product($this->cart['pid']) != false) $this->product_id = $this->cart['pid'];
        if (!$this->product_id)
            $this->product_id = wc_get_product_id_by_sku($this->sku);

        $this->already_exists = false;
    }

    /**
     * Initialize tigonwm_text
     * Define location_id, city_shortname
     * 
     * @return void
     */
    protected function generate_location_data() {
        $this->location_id = $this->cart['cartLocation']['locationId'];
        if($this->location_id === "Other") {
            $this->location_id = $this->cart['cartLocation']['latestStoreId'] ?? 'T1';
        }

        // Fallback to T1 if location is not recognized
        if (!isset(Attributes::$locations[$this->location_id])) {
            $this->location_id = 'T1';
        }

        $loc = Attributes::$locations[$this->location_id];
        $this->city_shortname = $loc['city_short'] ?? $loc['city'];

        $this->tigonwm_text = 'TIGON®';

        if ($this->location_id) {
            $this->tigonwm_text =
                ( $this->city_shortname ) .
                ' ' . $loc['st'];
        }
        if (isset($this->cart['isRental']) && $this->cart['isRental']){
            $this->tigonwm_text = 'TIGON® RENTALS';  
        }
    }

    /**
     * Initialize name, location_city_state,
     * define make_with_symbol, make_model_color, location_id
     *
     * @return void
     */
    protected function create_name()
    {
        $this->cart['cartType']['model'] = preg_replace('/\+$/', ' Plus', $this->cart['cartType']['model']);
        $this->cart['cartAttributes']['cartColor'] = ucwords($this->cart['cartAttributes']['cartColor']);

        $this->make_with_symbol = $this->cart['cartType']['make'] . '®';
        if ($this->cart['cartType']['model'] == 'Other') {
            $this->cart['cartType']['model'] = 'Golf Cart';
        }
        $this->make_model_color =
            strtoupper($this->make_with_symbol) . ' ' .
            strtoupper($this->cart['cartType']['model']) . ' ' .
            $this->cart['cartAttributes']['cartColor'];

        // Use schema template for name
        $templates = $this->get_schema_templates();
        $vars      = $this->build_template_variables();
        $name_tpl  = $templates['schema_name'] ?? '{^make}® {^model} {cartColor} in {city}, {stateAbbr}';

        $this->name = $this->evaluate_template($name_tpl, $vars, false);

        // Auto-generate SEO Title / Social Title
        $this->yoast_seo_title = $this->name . ' - Tigon Golf Carts';
    }

    /**
     * Initialize Slug,
     * define brand_hyphenated, pattern_hyphenated, color_hyphenated, location_hyphenated
     *
     * @return void
     */
    protected function create_slug()
    {
        $this->brand_hyphenated = preg_replace('/\s+/', '-', $this->cart['cartType']['make']);
        $this->pattern_hyphenated = preg_replace('/\s+/', '-', $this->cart['cartType']['model']);
        $this->color_hyphenated = preg_replace('/\s+/', '-', $this->cart['cartAttributes']['cartColor']);
        $this->seat_color_hyphenated = preg_replace('/\s+/', '-', $this->cart['cartAttributes']['seatColor']);
        $this->location_hyphenated = preg_replace('/\s+/', '-', Attributes::$locations[$this->location_id]['city'] . "-" . Attributes::$locations[$this->location_id]['st']);

        $templates = $this->get_schema_templates();
        $vars      = $this->build_template_variables();
        $slug_tpl  = $templates['schema_slug'] ?? '{make}-{model}-{cartColor}-seat-{seatColor}-{city}-{state}';

        // Generate slug from template and slugify
        $this->slug = $this->evaluate_template($slug_tpl, $vars, true);
        // throw new ErrorException($this->slug);
    }

    /**
     * Sideload and/or Initialize images
     *
     * @return void
     */
    protected function fetch_images()
    {
        /*
         * Images
         */
        $this->images = array();
        $i = 0;
        foreach ($this->cart['imageUrls'] as $remote_image_name) {
            $image_name = $this->generate_image_name($i);
            // Check if image already uploaded
            $site_image_url = '';
            $args = array(
                'post_type' => 'attachment',
                'name' => sanitize_title($image_name),
                'posts_per_page' => 1,
                'post_status' => 'inherit',
            );
            $_header = get_posts($args);
            $header = $_header ? array_pop($_header) : null;
            $site_image_id = $header ? $header->ID : '';

            $image_filename = preg_replace('/\s+/', '-', strtolower($image_name));
            $image_data = [
                'post_title' => $image_name,
                'post_content' => $this->name,
                'post_excerpt' => $this->name
            ];

            $image_meta = [
                '_wp_attachment_image_alt' => $this->name
            ];

            $new_image_id = \Tigon\DmsConnect\Includes\Somatic::attach_external_image(
                url: "$this->file_source/carts/$remote_image_name",
                filename: $image_filename,
                post_data: $image_data,
                metadata: $image_meta
            );
            if (!is_wp_error($new_image_id)) {
                array_push($this->images, $new_image_id);
            }
            $i++;
        }
        unset($i);
        //$alt_text = $this->make_with_symbol.' '.$this->cart['cartType']['model'].preg_replace('/[\|]*/', '', $this->images);
    }

    protected function generate_image_name($i)
    {
        $templates = $this->get_schema_templates();
        $vars      = $this->build_template_variables();
        $vars['index'] = $i + 1;

        $image_tpl = $templates['schema_image_name'] ?? '{^make}® {^model} {cartColor} in {city}, {stateAbbr} image';

        return $this->evaluate_template($image_tpl, $vars, false);
    }


    /**
     * Sideload and/or Initialize monroney_sticker
     *
     * @return void
     */
    protected function fetch_monroney()
    {
        add_filter('image_sideload_extensions', function ($accepted_extensions) {
            $accepted_extensions[] = 'pdf';
            return $accepted_extensions;
        });

        $this->monroney_sticker = null;

        if (isset($this->cart['_id'])) {
            $site_monroney_url = '';
            $monroney_name = $this->generate_monroney_name();

            $remote_monroney_name = $this->cart['_id'] . '.pdf';
            $args = array(
                'post_type' => 'attachment',
                'name' => sanitize_title($monroney_name),
                'posts_per_page' => 1,
                'post_status' => 'inherit',
            );
            $_mheader = get_posts($args);
            $mheader = $_mheader ? array_pop($_mheader) : false;
            $site_monroney_url = $mheader ? wp_get_attachment_url($mheader->ID) : '';

            // Delete outdated monroney
            if ($mheader !== false)
                wp_delete_post($mheader->ID, false);

            // $site_monroney_url = media_sideload_image(file: "https://s3.amazonaws.com/prod.docs.s3/cart-window-stickers/$remote_monroney_name", desc: $this->name . ' ' . $this->sku . ' Monroney Sticker', return_type: 'src');
            $monroney_filename = preg_replace('/\s+/', '-', strtolower($monroney_name));
            $monroney_data = [
                'post_title' => $monroney_name,
                'post_content' => $this->name,
                '_wp_attachment_image_alt' => $this->name
            ];

            $site_monroney_url = \Tigon\DmsConnect\Includes\Somatic::attach_external_image(
                url: "$this->file_source/cart-window-stickers/$remote_monroney_name",
                filename: $monroney_filename,
                post_data: $monroney_data,
                return: 'url'
            );



            if (is_wp_error($site_monroney_url))
                $site_monroney_url = '';

            $this->monroney_sticker = '[pdf-embedder url="' . $site_monroney_url . '"]';
        }
    }

    protected function generate_monroney_name()
    {
        $templates = $this->get_schema_templates();
        $vars      = $this->build_template_variables();

        $monroney_tpl = $templates['schema_monroney_name'] ?? '{^make}® {^model} {cartColor} in {city}, {stateAbbr} monroney';

        return $this->evaluate_template($monroney_tpl, $vars, false);
    }

    /**
     * Initialize categories, tags
     * define number_seats
     *
     * @return void
     */
    protected function attach_categories_tags()
    {
        // TODO tags need to be added as taxonomy terms
        $this->taxonomy_terms = [];
        // Formatting
        $cat_make_model = $this->make_with_symbol . ' ' . $this->cart['cartType']['model'];
        if ($this->cart['cartType']['model'] == 'DS') {
            $cat_make_model = strtoupper($this->make_with_symbol) . ' DS ELECTRIC';
        }

        if ($this->cart['cartAttributes']['passengers'] == 'Utility') {
            $this->cart['cartAttributes']['utilityBed'] = true;
            $cat_seats = '2 SEATER';
            $tag_seats = '2 SEATS';
        } else {
            $this->number_seats = explode(' ', $this->cart['cartAttributes']['passengers'])[0];
            $cat_seats = $this->number_seats . ' SEATER';
            $tag_seats = $this->number_seats . ' SEATS';
        }

        // make
        if (strtoupper($this->make_with_symbol) == 'SWIFT EV®') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->categories['SWIFT®'],
                $this->generated_attributes->tags['SWIFT®']
            );
        } else if (strtoupper($this->make_with_symbol) == 'EZGO®') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->categories['EZ-GO®'],
                $this->generated_attributes->tags['EZGO®']
            );
        } else {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->categories[strtoupper($this->make_with_symbol)],
                $this->generated_attributes->tags[strtoupper($this->make_with_symbol)]
            );
        }

        // make and model
        if (isset($this->generated_attributes->categories[strtoupper($cat_make_model)])) {
            if ($this->make_with_symbol === 'EZGO®') {
                array_push($this->taxonomy_terms, $this->generated_attributes->categories[strtoupper('EZ-GO® ' . $this->cart['cartType']['model'])]);
            } else {
                array_push($this->taxonomy_terms, $this->generated_attributes->categories[strtoupper($cat_make_model)]);
            }
        }


        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->tags[strtoupper($cat_make_model)],
            $this->generated_attributes->tags[strtoupper($this->make_model_color)],
            $this->generated_attributes->tags[strtoupper($this->name)]
        );

        //color
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->tags[strtoupper($this->cart['cartAttributes']['cartColor'])]
        );

        // seats
        if ($this->cart['cartAttributes']['passengers']) {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->categories[$cat_seats],
                $this->generated_attributes->tags[$tag_seats]
            );
        }

        // lifted
        if ($this->cart['cartAttributes']['isLifted']) {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->categories['LIFTED'],
                $this->generated_attributes->tags['LIFTED']
            );
        } else {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->tags['NON LIFTED']
            );
        }

        // new or used
        if (isset($this->cart['isRental']) && $this->cart['isRental']){
            array_push($this->taxonomy_terms, $this->generated_attributes->categories['RENTAL']);
        }
        
        if ($this->cart['isUsed']) {
            array_push($this->taxonomy_terms, $this->generated_attributes->categories['USED']);
        } else{
            array_push($this->taxonomy_terms, $this->generated_attributes->categories['NEW']);
        }

        if ($this->cart['isUsed']) {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->tags['USED']
            );
        } else
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->tags['NEW']
            );

        // location
        array_push(
            $this->taxonomy_terms,

            $this->generated_attributes->tags[strtoupper($this->city_shortname)],
            $this->generated_attributes->tags[strtoupper($this->tigonwm_text)],
            $this->generated_attributes->tags[strtoupper(Attributes::$locations[$this->location_id]['state'])],
            $this->generated_attributes->tags[strtoupper($this->city_shortname) . ' GOLF CART DEALERSHIP'],
            $this->generated_attributes->tags[strtoupper(Attributes::$locations[$this->location_id]['state']) . ' GOLF CART DEALERSHIP'],
            $this->generated_attributes->tags[strtoupper($this->city_shortname . ' ' . Attributes::$locations[$this->location_id]['state']) . ' STREET LEGAL DEALERSHIP']
        );

        // battery or gas
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->categories['GOLF CARTS'],
            $this->generated_attributes->tags['GOLF CART']
        );
        if ($this->cart['isElectric']) {
            array_push(
                $this->taxonomy_terms,

                $this->generated_attributes->categories['ELECTRIC'],
                $this->generated_attributes->tags['ELECTRIC']
            );

            array_push($this->taxonomy_terms, $this->generated_attributes->categories['ZERO EMISSION VEHICLES (ZEVS)']);

            if ($this->cart['battery']['type'] == 'Lead') {
                array_push(
                    $this->taxonomy_terms,

                    $this->generated_attributes->categories['LEAD-ACID'],
                    $this->generated_attributes->tags['LEAD-ACID']
                );
            } elseif ($this->cart['battery']['type'] == "Lithium") {
                array_push(
                    $this->taxonomy_terms,

                    $this->generated_attributes->categories['LITHIUM'],
                    $this->generated_attributes->tags['LITHIUM']
                );
            }
            array_push($this->taxonomy_terms, $this->generated_attributes->categories[$this->cart['battery']['packVoltage'] . " VOLT"]);

            if ($this->cart['title']['isStreetLegal']) {
                array_push(
                    $this->taxonomy_terms,

                    $this->generated_attributes->categories['STREET LEGAL'],
                    $this->generated_attributes->categories['NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)'],
                    $this->generated_attributes->categories['BATTERY ELECTRIC VEHICLES (BEVS)'],
                    $this->generated_attributes->categories['LOW SPEED VEHICLES (LSVS)'],
                    $this->generated_attributes->categories['MEDIUM SPEED VEHICLES (MSVS)'],

                    $this->generated_attributes->tags['NEV'],
                    $this->generated_attributes->tags['LSV'],
                    $this->generated_attributes->tags['MSV'],
                    $this->generated_attributes->tags['STREET LEGAL']
                );
            }
        } else {
            array_push(
                $this->taxonomy_terms,

                $this->generated_attributes->categories['GAS'],
                $this->generated_attributes->categories['PERSONAL TRANSPORTATION VEHICLES (PTVS)'],
                $this->generated_attributes->tags['GAS'],
                $this->generated_attributes->tags['PTV']
            );
        }

        // Inventory Type
         if(isset($this->cart['isRental']) && $this->cart['isRental'] ){
             //Rental
             if (!$this->cart['isUsed']) {
                array_push($this->taxonomy_terms, $this->generated_attributes->categories['LOCAL NEW RENTAL INVENTORY']);
            } else {
                array_push($this->taxonomy_terms, $this->generated_attributes->categories['LOCAL USED RENTAL INVENTORY']);
            }            
        }else{
            if (!$this->cart['isUsed']) {
                array_push($this->taxonomy_terms, $this->generated_attributes->categories['LOCAL NEW ACTIVE INVENTORY']);
            } else {
                array_push($this->taxonomy_terms, $this->generated_attributes->categories['LOCAL USED ACTIVE INVENTORY']);
            }
        } 

        // Drivetrain
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->categories['2X4']
        );

        // TIGON Dealership
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->categories['TIGON DEALERSHIP'],
            $this->generated_attributes->categories[strtoupper(
                'TIGON GOLF CARTS ' .
                $this->city_shortname .
                ' ' . Attributes::$locations[$this->location_id]['state']
            )],
        );

        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->tags['TIGON'],
            $this->generated_attributes->tags['TIGON GOLF CARTS']
        );

        /*
         * Primary Category ID
         */
        $this->primary_category = $this->generated_attributes->categories[strtoupper($this->make_with_symbol)];
    }


    protected function attach_attributes()
    {
        /*
         * Attributes
         */
        $this->attributes = array();

        // Battery Type
        if ($this->cart['isElectric']) {
            $this->attributes['pa_battery-type'] = $this->generated_attributes->attributes['battery-type']['object'];
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['battery-type']['options'][strtoupper($this->cart['battery']['type'])]
            );
            // Battery Warranty
            $this->attributes['pa_battery-warranty'] = $this->generated_attributes->attributes['battery-warranty']['object'];
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['battery-warranty']['options'][strtoupper($this->cart['battery']['warrantyLength'])]
            );
        }

        // Brush Guard — based on payload addedFeatures.brushGuard
        $this->attributes['pa_brush-guard'] = $this->generated_attributes->attributes['brush-guard']['object'];
        $brush_guard_value = (isset($this->cart['addedFeatures']['brushGuard']) && $this->cart['addedFeatures']['brushGuard']) ? 'YES' : 'NO';
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['brush-guard']['options'][$brush_guard_value]
        );

        // Cargo Rack — based on payload cartAttributes.cargoRack
        $this->attributes['pa_cargo-rack'] = $this->generated_attributes->attributes['cargo-rack']['object'];
        $cargo_rack_value = !empty($this->cart['cartAttributes']['cargoRack'])
            ? strtoupper($this->cart['cartAttributes']['cargoRack'])
            : 'NO';
        if (isset($this->generated_attributes->attributes['cargo-rack']['options'][$cargo_rack_value])) {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['cargo-rack']['options'][$cargo_rack_value]
            );
        } else {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['cargo-rack']['options']['NO']
            );
        }

        // Drivetrain
        $this->attributes['pa_drivetrain'] = $this->generated_attributes->attributes['drivetrain']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['drivetrain']['options']['2X4']
        );

        // Electric Bedlift
        $this->attributes['pa_electric-bed-lift'] = $this->generated_attributes->attributes['electric-bed-lift']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['electric-bed-lift']['options']['NO']
        );

        // Extended top
        $this->attributes['pa_extended-top'] = $this->generated_attributes->attributes['extended-top']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['extended-top']['options'][$this->cart['cartAttributes']['hasExtendedTop'] ? 'YES' : 'NO']
        );

        // Fender Flares
        $this->attributes['pa_fender-flares'] = $this->generated_attributes->attributes['fender-flares']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['fender-flares']['options']['YES']
        );

        // LED Accents
        if (strtoupper($this->make_with_symbol) == 'DENAGO®') {
            $this->attributes['pa_led-accents'] = $this->generated_attributes->attributes['led-accents']['object'];
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['led-accents']['options']['YES'],
                $this->generated_attributes->attributes['led-accents']['options']['LIGHT BAR']
            );
        } else {
            $this->attributes['pa_led-accents'] = $this->generated_attributes->attributes['led-accents']['object'];
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['led-accents']['options']['NO']
            );
        }

        // Lift kit
        $this->attributes['pa_lift-kit'] = $this->generated_attributes->attributes['lift-kit']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['lift-kit']['options'][($this->cart['cartAttributes']['isLifted'] ? '3 INCH' : 'NO')]
        );

        // Location
        $this->attributes['pa_location'] = $this->generated_attributes->attributes['location']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['location']['options'][strtoupper(Attributes::$locations[$this->location_id]['city'] . ' ' . Attributes::$locations[$this->location_id]['state'])],
            $this->generated_attributes->attributes['location']['options'][strtoupper(Attributes::$locations[$this->location_id]['state'])]
        );

        // Make Colors / Seat Colors
        $make_attrs = [
            "bintelli",
            "club-car",
            "denago",
            "epic",
            "evolution",
            "ezgo",
            "icon",
            "navitas",
            "polaris",
            "royal-ev",
            "star-ev",
            "swift",
            "tomberlin",
            "yamaha"
        ];
        $make_lower = strtolower($this->brand_hyphenated);

        // USED products always get pa_cart-color (with auto-creation of missing color terms)
        if ($this->cart['isUsed']) {
            $this->attributes['pa_cart-color'] = $this->generated_attributes->attributes['cart-color']['object'];
            $cart_color_upper = strtoupper($this->cart['cartAttributes']['cartColor']);
            if (isset($this->generated_attributes->attributes['cart-color']['options'][$cart_color_upper])) {
                array_push(
                    $this->taxonomy_terms,
                    $this->generated_attributes->attributes['cart-color']['options'][$cart_color_upper]
                );
            } else {
                // Color term doesn't exist — create it
                $new_term = wp_insert_term(
                    ucwords(strtolower($this->cart['cartAttributes']['cartColor'])),
                    'pa_cart-color',
                    ['slug' => sanitize_title($this->cart['cartAttributes']['cartColor'])]
                );
                if (!is_wp_error($new_term)) {
                    $this->generated_attributes->attributes['cart-color']['options'][$cart_color_upper] = $new_term['term_id'];
                    array_push($this->taxonomy_terms, $new_term['term_id']);
                }
            }

            $this->attributes['pa_seat-color'] = $this->generated_attributes->attributes['seat-color']['object'];
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['seat-color']['options'][strtoupper($this->cart['cartAttributes']['seatColor'])]
            );
        } elseif (array_search($make_lower, $make_attrs) !== false) {
            $this->attributes["pa_$make_lower-cart-colors"] = $this->generated_attributes->attributes[$make_lower . '-cart-colors']['object'];
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes[$make_lower . '-cart-colors']['options'][strtoupper($this->cart['cartAttributes']['cartColor'])]
            );

            $this->attributes["pa_$make_lower-seat-colors"] = $this->generated_attributes->attributes[$make_lower . '-seat-colors']['object'];
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes[$make_lower . '-seat-colors']['options'][strtoupper($this->cart['cartAttributes']['seatColor'])]
            );
        } else {
            $this->attributes['pa_cart-color'] = $this->generated_attributes->attributes['cart-color']['object'];
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['cart-color']['options'][strtoupper($this->cart['cartAttributes']['cartColor'])]
            );

            $this->attributes['pa_seat-color'] = $this->generated_attributes->attributes['seat-color']['object'];
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['seat-color']['options'][strtoupper($this->cart['cartAttributes']['seatColor'])]
            );
        }

        // Sound system
        $this->attributes['pa_sound-system'] = $this->generated_attributes->attributes['sound-system']['object'];
        array_push(
            $this->taxonomy_terms,
            (
                $this->generated_attributes->attributes['sound-system']['options'][strtoupper($this->make_with_symbol) . ' SOUND SYSTEM']
                ??
                $this->generated_attributes->attributes['sound-system']['options']['YES']
            )
        );

        // Passengers
        $attr_seats = $this->cart['cartAttributes']['passengers'] ?
            $this->number_seats . ' SEATER' :
            '';
        $this->attributes['pa_passengers'] = $this->generated_attributes->attributes['passengers']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['passengers']['options'][$attr_seats]
        );

        // Reciever Hitch
        $this->attributes['pa_receiver-hitch'] = $this->generated_attributes->attributes['receiver-hitch']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['receiver-hitch']['options']['NO']
        );

        // Return Policy
        $this->attributes['pa_return-policy'] = $this->generated_attributes->attributes['return-policy']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['return-policy']['options']['90 DAY'],
            $this->generated_attributes->attributes['return-policy']['options']['YES']
        );

        // Rim Size
        $this->attributes['pa_rim-size'] = $this->generated_attributes->attributes['rim-size']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['rim-size']['options'][$this->cart['cartAttributes']['tireRimSize'] . ' INCH']
        );

        // Shipping
        $this->attributes['pa_shipping'] = $this->generated_attributes->attributes['shipping']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['shipping']['options']['1 TO 3 DAYS LOCAL'],
            $this->generated_attributes->attributes['shipping']['options']['3 TO 7 DAYS OTR'],
            $this->generated_attributes->attributes['shipping']['options']['5 TO 9 DAYS NATIONAL']
        );
        // // TODO - Model Specific
        // // Side Step
        // if ($this->cart['cartAttributes']['sideStep']) {
        //     $this->attributes['pa_side-step'] = $this->generated_attributes->attributes['side-step']['object'];
        //     array_push(
        //         $this->taxonomy_terms,
        //         $this->generated_attributes->attributes['side-step']['options'][$this->cart['cartAttributes']['sideStep']]
        //     );
        // }

        // Street Legal
        $this->attributes['pa_street-legal'] = $this->generated_attributes->attributes['street-legal']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['street-legal']['options'][$this->cart['title']['isStreetLegal'] ? 'YES' : 'NO']
        );

        // Tire profile
        if ($this->cart['cartAttributes']['tireType']) {
            $this->attributes['pa_tire-profile'] = $this->generated_attributes->attributes['tire-profile']['object'];
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['tire-profile']['options'][strtoupper(preg_replace('/-/', ' ', $this->cart['cartAttributes']['tireType']))]
            );
        }

        // // TODO - Incomplete Data
        // // Utility Bed 
        // if ($this->cart['cartAttributes']['passengers'] === 'Utility') {
        //     $this->attributes['pa_utility-bed'] = $this->generated_attributes->attributes['utility-bed']['object'];
        //     array_push(
        //         $this->taxonomy_terms,
        //         $this->generated_attributes->attributes['utility-bed']['options']['']
        //     );
        // }

        // Vehicle Class
        $vehicle_class_attr = ['Golf Cart'];
        if ($this->cart['isElectric']) {
            array_push($vehicle_class_attr, 'Neighborhood Electric Vehicles (NEVs)');
            array_push($vehicle_class_attr, 'Zero Emission Vehicles (ZEVs)');
            if ($this->cart['title']['isStreetLegal']) {
                array_push($vehicle_class_attr, 'Low Speed Vehicle (LSVs)');
                array_push($vehicle_class_attr, 'Medium Speed Vehicle (MSVs)');
            }
        }
        if ($this->cart['title']['isStreetLegal'])
            array_push($vehicle_class_attr, 'Personal Transportation Vehicles (PTVs)');

        if ($this->cart['cartAttributes']['utilityBed'])
            array_push($vehicle_class_attr, 'Utility Task Vehicle (UTVs)');

        $this->attributes['pa_vehicle-class'] = $this->generated_attributes->attributes['vehicle-class']['object'];
        foreach ($vehicle_class_attr as $vehicle_class) {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['vehicle-class']['options'][strtoupper($vehicle_class)]
            );
        }

        // Vehicle Warranty
        $this->attributes['pa_vehicle-warranty'] = $this->generated_attributes->attributes['vehicle-warranty']['object'];
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->attributes['vehicle-warranty']['options'][strtoupper($this->cart['warrantyLength'])]
        );

        // Year of Vehicle
        if ($this->cart['cartType']['year']) {
            $this->attributes['pa_year-of-vehicle'] = $this->generated_attributes->attributes['year-of-vehicle']['object'];
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->attributes['year-of-vehicle']['options'][strtoupper($this->cart['cartType']['year'])]
            );
        }

        // Serialize for database storage
        $this->attributes = serialize($this->attributes);
    }


    protected function attach_taxonomies()
    {
        // Location
        array_push($this->taxonomy_terms, Attributes::$locations[$this->location_id]['city_id']);
        array_push($this->taxonomy_terms, Attributes::$locations[$this->location_id]['state_id']);
        $this->primary_location = Attributes::$locations[$this->location_id]['city_id'];

        // Manufacturers
        if (strtoupper($this->make_with_symbol) == 'SWIFT EV®') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->manufacturers_taxonomy['SWIFT®']
            );
        } else if (strtoupper($this->make_with_symbol) == 'STAR®') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->manufacturers_taxonomy['STAR EV®']
            );
        } else {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->manufacturers_taxonomy[strtoupper($this->make_with_symbol)]
            );
        }

        // Models
        if ($this->cart['cartType']['model'] == 'DS') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->models_taxonomy[strtoupper($this->make_with_symbol) . ' DS ELECTRIC']
            );
        } else if ($this->cart['cartType']['model'] == 'Precedent') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->models_taxonomy[strtoupper($this->make_with_symbol) . ' PRECEDENT ELECTRIC']
            );
        } else if ($this->cart['cartType']['model'] == '4L') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->models_taxonomy[strtoupper($this->make_with_symbol) . ' CROWN 4 LIFTED']
            );
        } else if ($this->cart['cartType']['model'] == '6L') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->models_taxonomy[strtoupper($this->make_with_symbol) . ' CROWN 6 LIFTED']
            );
        } else if ($this->cart['cartType']['model'] == 'Drive 2') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->models_taxonomy[strtoupper($this->make_with_symbol) . ' DRIVE2']
            );
        } else if (strtoupper($this->make_with_symbol) == 'STAR®') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->models_taxonomy['STAR EV®' . ' ' . strtoupper($this->cart['cartType']['model'])]
            );
        } else if (strtoupper($this->make_with_symbol) == 'EZGO®') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->models_taxonomy['EZ-GO®' . ' ' . strtoupper($this->cart['cartType']['model'])]
            );
        } else {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->models_taxonomy[strtoupper($this->make_with_symbol . ' ' . $this->cart['cartType']['model'])]
            );
        }

        // Sound Systems
        if (strtoupper($this->make_with_symbol) == 'SWIFT®') {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->sound_systems_taxonomy['SWIFT EV® SOUND SYSTEM']
            );
        } else {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->sound_systems_taxonomy[strtoupper($this->make_with_symbol) . ' SOUND SYSTEM']
            );
        }

        // Added Features
        if (isset($this->cart['addedFeatures'])) {
            if ($this->cart['addedFeatures']['staticStock'])
                array_push($this->taxonomy_terms, $this->generated_attributes->added_features_taxonomy['STATIC STOCK']);


            if ($this->cart['addedFeatures']['brushGuard'])
                array_push($this->taxonomy_terms, $this->generated_attributes->added_features_taxonomy['BRUSH GUARD']);


            if ($this->cart['addedFeatures']['clayBasket'])
                array_push($this->taxonomy_terms, $this->generated_attributes->added_features_taxonomy['CLAY BASKET']);


            if ($this->cart['addedFeatures']['fenderFlares'])
                array_push($this->taxonomy_terms, $this->generated_attributes->added_features_taxonomy['FENDER FLARES']);

            if ($this->cart['addedFeatures']['LEDs'])
                array_push($this->taxonomy_terms, $this->generated_attributes->added_features_taxonomy['LEDS']);

            if ($this->cart['addedFeatures']['lightBar'])
                array_push($this->taxonomy_terms, $this->generated_attributes->added_features_taxonomy['LIGHT BAR']);

            if ($this->cart['addedFeatures']['underGlow'])
                array_push($this->taxonomy_terms, $this->generated_attributes->added_features_taxonomy['UNDER GLOW']);

            if ($this->cart['cartAttributes']['isLifted'])
                array_push($this->taxonomy_terms, $this->generated_attributes->added_features_taxonomy['LIFT KIT']);

            if ($this->cart['cartAttributes']['hitch'])
                array_push($this->taxonomy_terms, $this->generated_attributes->added_features_taxonomy['TOW HITCH']);

            if ($this->cart['addedFeatures']['stockOptions'])
                array_push($this->taxonomy_terms, $this->generated_attributes->added_features_taxonomy['STOCK OPTIONS']);
        }

        // Vehicle class taxonomy
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->vehicle_classes_taxonomy['GOLF CART']
        );

        if ($this->cart['isElectric']) {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->vehicle_classes_taxonomy['ZERO EMISSION VEHICLES (ZEVS)']
            );
            if ($this->cart['title']['isStreetLegal']) {
                array_push(
                    $this->taxonomy_terms,
                    $this->generated_attributes->vehicle_classes_taxonomy['LOW SPEED VEHICLE (LSVS)']
                );
                array_push(
                    $this->taxonomy_terms,
                    $this->generated_attributes->vehicle_classes_taxonomy['MEDIUM SPEED VEHICLE (MSVS)']
                );
                array_push(
                    $this->taxonomy_terms,
                    $this->generated_attributes->vehicle_classes_taxonomy['NEIGHBORHOOD ELECTRIC VEHICLES (NEVS)']
                );
            }
        }
        if ($this->cart['title']['isStreetLegal']) {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->vehicle_classes_taxonomy['PERSONAL TRANSPORTATION VEHICLES (PTVS)']
            );
        }
        if ($this->cart['cartAttributes']['utilityBed']) {
            array_push(
                $this->taxonomy_terms,
                $this->generated_attributes->vehicle_classes_taxonomy['UTILITY TASK VEHICLE (UTVS)']
            );
        }

        // Inventory status taxonomy
        if(isset($this->cart['isRental']) && $this->cart['isRental']){
            //Rental
            if (!$this->cart['isUsed']) {
                array_push($this->taxonomy_terms, $this->generated_attributes->inventory_status_taxonomy['LOCAL NEW RENTAL INVENTORY']);
            } else {
                array_push($this->taxonomy_terms, $this->generated_attributes->inventory_status_taxonomy['LOCAL USED RENTAL INVENTORY']);
            }
        }else{
            if ($this->cart['isUsed']) {
                array_push(
                    $this->taxonomy_terms,
                    $this->generated_attributes->inventory_status_taxonomy['LOCAL USED ACTIVE INVENTORY']
                );
            } else {
                array_push(
                    $this->taxonomy_terms,
                    $this->generated_attributes->inventory_status_taxonomy['LOCAL NEW ACTIVE INVENTORY']
                );
            } 
        }

        // Drivetrain taxonomy
        array_push(
            $this->taxonomy_terms,
            $this->generated_attributes->drivetrains_taxonomy[
                $this->cart['cartAttributes']['driveTrain'] ?? '2X4'
            ]
        );
    }

    protected function attach_custom_options()
    {
        $this->custom_product_options = array();
        if (!$this->cart['isUsed']) {
            // Special name handling
            if (strtoupper($this->make_with_symbol) == 'DENAGO®') {
                // e.g. Denago® EV Nomad XL Add Ons
                $add_on_list = 'Denago® EV ' . $this->cart['cartType']['model'] . ' Add Ons';
            } elseif (strtoupper($this->make_with_symbol) == 'EPIC®') {
                // e.g. EPIC® E40 Add Ons
                $add_on_list = 'EPIC® ' . $this->cart['cartType']['model'] . ' Add Ons';
            } elseif (strtoupper($this->make_with_symbol) == 'EVOLUTION®') {
                // e.g. EVolution® Carrier 6 Plus Add Ons
                $add_on_list = 'EVolution® ' . $this->cart['cartType']['model'] . ' Add Ons';

                if (substr($this->cart['cartType']['model'], 0, 3) == 'D5 ') {
                    // e.g. EVolution® D5-Maverick 4 Plus Add Ons
                    $model = $this->cart['cartType']['model'];
                    $pos = strpos($model, ' ');
                    $model = substr_replace($model, '-', $pos, 1);

                    $add_on_list = 'EVolution® ' . $model . ' Add Ons';
                }
            } elseif (strtoupper($this->make_with_symbol) == 'ICON®') {
                // e.g. ICON® i60L Add Ons
                $add_on_list = 'ICON® ' . $this->cart['cartType']['model'] . ' Add Ons';
            } elseif (strtoupper($this->make_with_symbol) == 'SWIFT EV®') {
                // e.g. SWIFT EV® Mach 4E Add Ons
                $add_on_list = 'SWIFT EV® ' . $this->cart['cartType']['model'] . ' Add Ons';
            } else
                $add_on_list = $this->make_with_symbol . ' ' . $this->cart['cartType']['model'] . ' Add Ons';

            array_push(
                $this->custom_product_options,
                $this->generated_attributes->custom_options[$add_on_list]
            );
        } else {
            $addons = array_flip($this->cart['advertising']['cartAddOns'] ?? []);

            if (isset($addons['Golf cart enclosure 2 passenger 600']) && $this->number_seats == 2)
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['2 Passenger Golf Cart Enclosure']
                );
            if (isset($addons['Golf cart enclosure 4 Passenger 800']) && $this->number_seats == 4)
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['4 Passenger Golf Cart Enclosure']
                );
            if (isset($addons['Golf cart enclosure 6 passenger 1200']) && $this->number_seats == 6)
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['6 Passenger Golf Cart Enclosure']
                );
            if (isset($addons['120 Volt inverter 500']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['120 Volt Inverter']
                );
            if (isset($addons['32 inch light bar 220']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['32in LED Light Bar']
                );
            if (isset($addons['Cargo caddie 250']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Cargo Caddie']
                );
            if (isset($addons['Rear seat cupholders 80']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Rear Seat Cupholders']
                );
            if (isset($addons['Upgraded charger 210']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Upgraded Charger']
                );
            if (isset($addons['Breezeasy Fan System 400']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Breezeasy 3 Fan System']
                );
            if (isset($addons['Golf bag attachment 120']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Golf Bag Attachment']
                );
            if (isset($addons['Led light kit 350']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['LED Cart Light Kit']
                );
            if (isset($addons['Led light kit with signals and horn 495']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['LED Cart Light Kit With Signals & Horn']
                );
            if (isset($addons['Led under glow 400']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['LED Under Glow Lights']
                );
            if (isset($addons['Led roof lights 400']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['LED Roof Lights']
                );
            if (isset($addons['Rear seat kit 385']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Rear Seat Kit']
                );
            if (isset($addons['Basic 4 Passenger storage cover 150']) && $this->number_seats == 4)
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Basic 4 Passenger Storage Cover']
                );
            if (isset($addons['Premium 4 Passenger storage cover 300']) && $this->number_seats == 4)
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Premium 4 Passenger Storage Cover']
                );
            if (isset($addons['Premium 6 Passenger storage cover 385']) && $this->number_seats == 6)
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Premium 6 Passenger Storage Cover']
                );
            if (isset($addons['26 in sound bar 500']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['26" Sound Bar']
                );
            if (isset($addons['32 in Sound bar 600']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['32" Sound Bar']
                );
            if (isset($addons['EcoXGear subwoofer 745']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['EcoXGear Subwoofer']
                );
            if (isset($addons['New tinted windshield 210']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Tinted Windshield']
                );
            if (
                isset($addons['Hitch 80']) &&
                isset($addons['Hitch 300']) &&
                isset($addons['Hitch 500'])
            ) {
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Hitch Bolt On']
                );
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Basic Hitch Weld On']
                );
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Premium Hitch Weld On']
                );
            }
            if (isset($addons['Seat belts 4 Passenger 160']) && $this->number_seats == 4)
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Seat Belts 4 Passenger']
                );
            if (isset($addons['Seat belts 6 Passenger 240']) && $this->number_seats == 6)
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Seat Belts 6 Passenger']
                );
            if (isset($addons['Grab bar 85']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Grab Bar']
                );
            if (isset($addons['Deluxe Grab Bar 150']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Deluxe Grab Bar']
                );
            if (isset($addons['Side mirrors 65']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Side Mirrors']
                );
            if (isset($addons['Extended roof 500']))
                array_push(
                    $this->custom_product_options,
                    $this->generated_attributes->custom_options['Extended Roof 84"']
                );
        }

        $this->custom_product_options = serialize($this->custom_product_options);
    }

    protected function attach_custom_tabs()
    {
        $tab_names = array();
        if ($this->cart['isUsed']) {
            array_push($tab_names, "TIGON Warranty (USED GOLF CARTS)");
        }
        switch (strtoupper($this->make_with_symbol)) {

            case "DENAGO®":
                if (!$this->cart['isUsed'])
                    array_push($tab_names, "DENAGO Warranty");

                if ($this->cart['cartType']['year'] == '2024')
                    array_push($tab_names, "VIDEO DENAGO 2024");

                switch (strtoupper($this->cart['cartType']['model'])) {
                    case "NOMAD":
                        array_push($tab_names, "Denago® Nomad Vehicle Specs");
                        break;
                    case "NOMAD XL":
                        array_push($tab_names, "Denago® Nomad XL Vehicle Specs");
                        array_push($tab_names, "Denago Nomad XL User Manual");
                        if ($this->cart['cartType']['year'] == '2024') array_push($tab_names, "PICS DENAGO NOMAD XL 2024");
                        break;
                    case "ROVER XL":
                        array_push($tab_names, "Denago® Rover XL Vehicle Specs");
                        if ($this->cart['cartType']['year'] == '2024') array_push($tab_names, "PICS DENAGO ROVER XL 2024");
                        break;
                }
                break;
            case "EVOLUTION®":
                if (!$this->cart['isUsed'])
                    array_push($tab_names, "EVolution Warranty");

                switch (strtoupper($this->cart['cartType']['model'])) {
                    case "CLASSIC 2 PRO":
                        array_push($tab_names, "EVolution Classic 2 Pro Images");
                        array_push($tab_names, "EVolution Classic 2 Pro Specs");
                        break;
                    case "CLASSIC 2 PLUS":
                        array_push($tab_names, "EVolution Classic 2 Plus Images");
                        array_push($tab_names, "EVolution Classic 2 Plus Specs");
                        break;
                    case "CLASSIC 4 PRO":
                        array_push($tab_names, "EVolution Classic 4 Pro Images");
                        array_push($tab_names, "EVolution Classic 4 Pro Specs");
                        break;
                    case "CLASSIC 4 PLUS":
                        array_push($tab_names, "EVolution Classic 4 Plus Images");
                        array_push($tab_names, "EVolution Classic 4 Plus Specs");
                        break;
                    case 'D5 MAVERICK 2+2':
                        array_push($tab_names, 'EVolution D5-Maverick 2+2');
                        array_push($tab_names, 'EVolution D5-Maverick 2+2 Images');
                        break;
                    case 'D5 MAVERICK 2+2 PLUS':
                        array_push($tab_names, 'EVolution D5-Maverick 2+2 Plus Images');
                        break;
                    case 'D5 RANGER 2+2':
                        array_push($tab_names, 'EVOLUTION D5 RANGER 2+2 IMAGES');
                        array_push($tab_names, 'EVOLUTION D5 RANGER 2+2 SPECS');
                        break;
                    case 'D5 RANGER 2+2 PLUS':
                        array_push($tab_names, 'EVOLUTION D5 RANGER 2+2 PLUS IMAGES');
                        array_push($tab_names, 'EVOLUTION D5 RANGER 2+2 PLUS SPECS');
                        break;
                    case 'D5 RANGER 4':
                        array_push($tab_names, 'EVOLUTION D5 RANGER 4 IMAGES');
                        array_push($tab_names, 'EVOLUTION D5 RANGER 4 SPEC');
                        break;
                    case 'D5 RANGER 4 PLUS':
                        array_push($tab_names, 'EVOLUTION D5 RANGER 4 PLUS IMAGES');
                        array_push($tab_names, 'EVOLUTION D5 RANGER 4 PLUS SPECS');
                        break;
                    case 'D5 RANGER 6':
                        array_push($tab_names, 'EVOLUTION D5 RANGER 6 IMAGES');
                        array_push($tab_names, 'EVOLUTION D5 RANGER 6 SPECS');
                        break;
                }
                break;
        }


        $tabs = array_map(function ($name) {
            $tab = [
                "name" => $name,
                'id' => $this->generated_attributes->tabs[$name]['tab_id'],
                "title" => $this->generated_attributes->tabs[$name]['tab_title'],
                "content" => preg_replace('/\\\*(&quot;)*/', '', $this->generated_attributes->tabs[$name]['tab_content'])
            ];
            return $tab;
        }, $tab_names);
        $this->custom_tabs = serialize($tabs);
    }

    protected function generate_descriptions()
    {
        /*
         * Meta Description
         */
        $this->meta_description = $this->make_model_color . ' At TIGON Golf Carts in ' . $this->tigonwm_text .
            '. Call Now ' . Attributes::$locations[$this->location_id]['phone'] . ' Get 0% Financing, and Shipping Options Today!';

        /**
         * Description and Short Description
         */
        switch (strtoupper($this->cart['cartType']['make'])) {
            case 'DENAGO':
                $make_dealer_format = $this->brand_hyphenated . '-ev';
                break;
            default:
                $make_dealer_format = $this->brand_hyphenated;
                break;
        }
        $make_hyperlink = '<a href="https://tigongolfcarts.com/' . $make_dealer_format . '">' . $this->make_with_symbol . '</a>';

        switch (strtoupper($this->cart['cartType']['model'])) {
            case 'TURFMAN 200':
            case 'TURFMAN 800':
            case 'TURFMAN 1000':
                $model_dealer_format = preg_replace('/(^.+?)-(?=[0-9])/', '$1/u-', $this->pattern_hyphenated);
                break;
            default:
                $model_dealer_format = preg_replace('/(^.+?)-(?=[0-9])/', '$1/', $this->pattern_hyphenated);
                break;
        }
        $model_hyperlink = '<a href="https://tigongolfcarts.com/' . $make_dealer_format . '/' . $model_dealer_format . '">' . $this->cart['cartType']['model'] . '</a>';

        $adjectives = [
            'elegant',
            'unbeatable',
            'exceptional',
            'versatile',
            'dependable',
            'stylish',
            'eye-catching',
            'proven and reliable',
            'sleek'
        ];
        $sd_intros = [
            "Introducing the $make_hyperlink " . $model_hyperlink . " from Tigon Golf Carts,",
            "Experience the freedom to explore with this " . $this->cart['cartAttributes']['cartColor'] . " $make_hyperlink " . $model_hyperlink . ",",
            "The $make_hyperlink " . $model_hyperlink . " is taking the industry by storm as",
            "Conquer the terrain with the $make_hyperlink " . $model_hyperlink . " in " . $this->cart['cartAttributes']['cartColor'] . ",",
            "Take the reigns of the " . $this->cart['cartAttributes']['cartColor'] . " $make_hyperlink " . $model_hyperlink . " from Tigon Golf Carts,",
        ];
        $sd_outros = [
            'this ' . $adjectives[random_int(0, 8)] . ' cart is perfect for both on and off-course adventures.',
            'the ' . $adjectives[random_int(0, 8)] . ' ' . $this->make_with_symbol . ' ' . $this->cart["cartType"]['model'] . ' is the perfect companion for all your journeys.',
            'this ' . $adjectives[random_int(0, 8)] . ' machine is a cart you don\'t want to miss!',
            'this ' . $adjectives[random_int(0, 8)] . ' vehicle sets a new standard for luxury and efficiency.'
        ];

        $sd_engine_specs = !empty($this->cart['engine']['horsepower']) ? $this->cart['engine']['horsepower'] . ' horsepower' : 'high quality';

        if (!$this->already_exists) {
            $this->short_description = '<h2 id="' . $this->pattern_hyphenated . '" style="text-align: center;">' . $this->name . '</h2><p style="text-align: center;">';
            $this->short_description = $this->short_description . $sd_intros[random_int(0, 4)];
            if ($this->cart['cartAttributes']['utilityBed']) {
                $this->short_description = $this->short_description . ' a sturdy workhorse ready to help you get the job done.</p>' .
                    '<p style="text-align: center;">Featuring a built-in utility bed, the ' . $this->cart["cartType"]['model'] . ' is highly capable and versatile.';
            } elseif ($this->cart['isElectric']) {
                $this->short_description = $this->short_description . ' an elegant powerhouse designed for adventure seekers.</p>' .
                    '<p style="text-align: center;">Equipped with a powerful ' . $this->cart['battery']['packVoltage'] . ' volt electric motor, the ' . $this->cart["cartType"]['model'] .
                    ' provides a clean, reliable ride without sacrificing performance. ';
            } else {
                $this->short_description = $this->short_description . ' a rugged beast ready to help you take on the world.</p>' .
                    '<p style="text-align: center;">With a ' . $sd_engine_specs . ' gas engine, the ' . $this->cart["cartType"]['model'] . ' is a powerhouse of performance. ';
            }
            if ($this->number_seats == 6) {
                $this->short_description = $this->short_description . 'Capable of carting 6 passengers, ';
            } else
                $this->short_description = $this->short_description . 'Combining rugged durability with sophisticated technology, ';
            $this->short_description = $this->short_description . $sd_outros[random_int(0, 3)] . '</p>';
        } else
            $this->short_description = null;

        // Description
        $desc_street_legal = ($this->cart['title']['isStreetLegal']) ? ('<tr><td>Street Legal</td><td>Fully Street Legal</td></tr>') : ('');

        $description_features = [];
        // TODO Unimplemented
        if ($this->cart['cartAttributes']['isLifted'])
            array_push($description_features, '3 Inch Lift Kit');
        /*if ($this->cart['cartAttributes']['hitch'] == 'yes')
            array_push($this->description_features, "Reciever Hitch");*/
        if ($this->cart['cartAttributes']['hasSoundSystem'])
            array_push($description_features, "$this->make_with_symbol Sound System");
        /*if ($this->cart['cartAttributes']['cargoRack'])
            array_push($this->description_features, $this->cart['cartAttributes']['cargoRack']);
        */
        foreach (($this->cart['advertising']['cartAddOns'] ?? []) as $addon) {
            $formatted_addon = explode(' ', $addon);
            array_pop($formatted_addon);
            $formatted_addon = implode(' ', $formatted_addon);
            array_push($description_features, $formatted_addon);
        }

        $desc_feat_markup = (count($description_features) > 0) ? ("<tr><td>Additional Features</td><td>" . implode(', ', $description_features) . "</td></tr>") : ("");
        $desc_leds_markup = '';
        // // TODO Unimplemented
        // if (is_countable($this->cart['cartAttributes']['LEDs']))
        //     $desc_leds_markup = (count($this->cart['cartAttributes']['LEDs']) > 0) ? ("<tr><td>LEDs</td><td>" . implode(', ', $this->cart['cartAttributes']['LEDs']) . "</td></tr>") : ("");

        $desc_power = '';
        $acdc = ($this->cart['battery']['isDC']) ? ("DC") : ("AC");
        if ($this->cart['isElectric']) {
            $desc_power = '<tr><td>Battery</td><td>' . $this->cart['battery']['type'] . ' ' . $acdc . ' Battery</td></tr>' .
                '<tr><td>Battery Brand</td><td>' . $this->cart['battery']['brand'] . '</td></tr>' .
                '<tr><td>Battery Year</td><td>' . $this->cart['battery']['year'] . '</td></tr>';
            if ($this->cart['battery']['ampHours']) {
                $desc_power = $desc_power . '<tr><td>Capacity</td><td>' . $this->cart['battery']['ampHours'] . ' Amp Hours</td></tr>';
            }
            $desc_power = $desc_power . '<tr><td>Warranty</td><td>' . $this->cart['warrantyLength'] . ' parts, ' . $this->cart['battery']['warrantyLength'] . ' battery warranty</td></tr>';
        } else {
            $desc_power = '<tr><td>Engine</td><td>' . $this->cart['cartType']['year'] . ' ' . $this->cart['engine']['make'] . /*' ' . $this->cart['engine']['model'] .*/ '</td></tr>';
            if ($this->cart['engine']['stroke'] != null && $this->cart['engine']['horsepower'] != null) {
                $desc_power = $desc_power . '<tr><td>Specs</td><td>' . $this->cart['engine']['stroke'] . ' Stroke, ' . $this->cart['engine']['horsepower'] . ' HP</td></tr>';
            }
            $desc_power = $desc_power . '<tr><td>Warranty</td><td>' . $this->cart['warrantyLength'] . ' parts and engine warranty</td></tr>';
        }

        $this->description = '<h2 id="' . $this->pattern_hyphenated . '" style="text-align: center;"><strong>' . $this->name . '</strong></h2>' .
            '<table style="text-align: center;"><thead><tr><th>Feature</th><th>Description</th></tr></thead>' .
            '<tbody>' .
            "<tr><td>Make</td><td>$make_hyperlink</td></tr>" .
            "<tr><td>Model</td><td>$model_hyperlink</td></tr>" .
            "<tr><td>Year</td><td>" . $this->cart['cartType']['year'] . "</td></tr>" .
            $desc_street_legal .
            "<tr><td>Color</td><td>" . $this->cart['cartAttributes']['cartColor'] . "</td></tr>" .
            "<tr><td>Seat Color</td><td>" . $this->cart['cartAttributes']['seatColor'] . "</td></tr>" .
            "<tr><td>Tires</td><td>" . $this->cart['cartAttributes']['tireType'] . "</td></tr>" .
            "<tr><td>Rims</td><td>" . $this->cart['cartAttributes']['tireRimSize'] . "\"</td></tr>" .
            $desc_leds_markup .
            $desc_feat_markup .
            $desc_power;

        // foreach($this->attributes as $attr) {
        //     $this->description = $this->description.'<tr><td>'.$attr['id'].'</td><td>'.$attr['options'].'</td></tr>';
        // }

        $this->description = $this->description . '</tbody></table>';

        // Apply schema overrides for description / short description if provided
        $templates = $this->get_schema_templates();
        $vars      = $this->build_template_variables();

        if (!empty(trim($templates['schema_short_description'] ?? ''))) {
            $this->short_description = $this->evaluate_template($templates['schema_short_description'], $vars, false);
        }
        if (!empty(trim($templates['schema_description'] ?? ''))) {
            $this->description = $this->evaluate_template($templates['schema_description'], $vars, false);
        }

        // Call Tigon Golf Carts shortcode
        $call_link = [
            'type' => 'external',
            'post' => 0,
            'post_label' => '',
            'url' => 'tel:' . Attributes::$locations[$this->location_id]['phone'],
            'image_id' => 0,
            'image_url' => '',
            'title' => 'CALL TIGON GOLF CARTS',
            'summary' => '',
            'template' => 'use_default_from_settings'
        ];
        $call_link_json = json_encode($call_link);

        $call_shortcode = '[visual-link-preview encoded="' . base64_encode($call_link_json) . '"]';

        $this->description = $this->description . $call_shortcode;
    }

    protected function set_simple_fields()
    {
        // Date added
        $date = date("Y-m-d H:i:s", $this->cart['inventoryTimestamp']);

        //Date updated
        $post_modified_date = date("Y-m-d H:i:s");

        // Stock
        $instock_str = $this->cart['isInStock'] ? "yes" : "no";
        $stock_qty = 0;

        $this->method = '';
        if ($this->cart['isInStock'] && !$this->cart['isInBoneyard'] && $this->cart['advertising']['needOnWebsite']) {
            if ($this->product_id) {
                $this->method = 'update';
            } else $this->method = 'create';
        } else $this->method = 'delete';


        // Simple logic/static fields
        $this->post_type = "product";
        $this->published = "publish"; //publish, pending, protected, draft
        $this->comment_status = 'open';
        $this->ping_status = 'closed';
        $this->menu_order = '0';
        $this->comment_count = '0';
        $this->post_author = '3';

        $this->price = $this->cart['retailPrice'];
        $this->sale_price = $this->cart['salePrice'];
        $this->tax_status = "taxable";
        $this->tax_class = "standard";
        $this->in_stock = $this->cart['isInStock'] ? 'instock' : 'outofstock';
        $this->manage_stock = 'no'; //instock, outofstock, onbackorder
        $this->backorders_allowed = 'no'; //notify, yes
        $this->sold_individually = 'no';
        $this->is_virtual = 'no';
        $this->downloadable = 'no';
        $this->download_limit = '-1';
        $this->download_expiry = '-1';
        array_push($this->taxonomy_terms, 665);//shipping class
        $this->bit_is_cornerstone = '1';
        $this->attr_exclude_global_forms = '1';
        $this->stock = 10000;

        $this->condition = $this->cart['isUsed'] ? 'used' : 'new';
        $this->google_brand = strtoupper($this->make_with_symbol);
        $this->google_color = strtoupper($this->cart['cartAttributes']['cartColor']);
        $this->google_pattern = $this->cart['cartType']['model'];
        $this->google_size_system = 'US';
        $this->gender = 'unisex';
        $this->adult_content = 'no';
        $this->age_group = 'all ages';
        $this->google_category = 'Vehicles & Parts > Vehicles > Motor Vehicles > Golf Carts';
        $this->product_image_source = 'product';
        $this->facebook_sync = 'yes';
        $this->facebook_visibility = 'yes';

        $this->monroney_container_id = 'field_66e3332abf481';

    }

    /**
     * Apply user-configured field mappings from the Field Mapping admin page.
     *
     * Reads enabled mappings from the database and applies the resolved DMS
     * values to the corresponding Abstract_Cart properties, which are then
     * passed into the Database_Object constructor.
     */
    protected function apply_custom_field_mappings(): void
    {
        if (!class_exists('\Tigon\DmsConnect\Admin\Field_Mapping')) {
            return;
        }

        $resolved = \Tigon\DmsConnect\Admin\Field_Mapping::apply($this->cart);

        // Map postmeta targets to Abstract_Cart properties.
        // This bridges the "meta key" → "property name" gap.
        $meta_to_property = [
            '_sku'            => 'sku',
            '_regular_price'  => 'price',
            '_price'          => 'price',
            '_sale_price'     => 'sale_price',
            '_stock_status'   => 'in_stock',
            '_manage_stock'   => 'manage_stock',
            '_tax_status'     => 'tax_status',
            '_tax_class'      => 'tax_class',
            '_virtual'        => 'is_virtual',
            '_downloadable'   => 'downloadable',
            '_sold_individually' => 'sold_individually',
            '_backorders'     => 'backorders_allowed',
            '_yoast_wpseo_title'    => 'yoast_seo_title',
            '_yoast_wpseo_metadesc' => 'meta_description',
            '_wc_gla_brand'         => 'google_brand',
            '_wc_gla_color'         => 'google_color',
            '_wc_gla_pattern'       => 'google_pattern',
            '_wc_gla_condition'     => 'condition',
        ];

        foreach ($resolved['postmeta'] as $key => $value) {
            if (isset($meta_to_property[$key])) {
                $prop = $meta_to_property[$key];
                $this->$prop = $value;
            }
        }

        // Map post field targets.
        $post_to_property = [
            'post_title'   => 'name',
            'post_name'    => 'slug',
            'post_content' => 'description',
            'post_excerpt' => 'short_description',
            'post_status'  => 'published',
            'menu_order'   => 'menu_order',
        ];

        foreach ($resolved['post'] as $key => $value) {
            if (isset($post_to_property[$key])) {
                $prop = $post_to_property[$key];
                $this->$prop = $value;
            }
        }

        // Map taxonomy targets to term IDs and add to taxonomy_terms.
        foreach ($resolved['taxonomy'] as $taxonomy => $term_names) {
            foreach ($term_names as $term_name) {
                $term = get_term_by('name', $term_name, $taxonomy);
                if (!$term) {
                    $term = get_term_by('slug', sanitize_title($term_name), $taxonomy);
                }
                if ($term && !is_wp_error($term)) {
                    if (!in_array($term->term_taxonomy_id, $this->taxonomy_terms)) {
                        $this->taxonomy_terms[] = $term->term_taxonomy_id;
                    }
                }
            }
        }
    }

    protected function field_overrides()
    {
    }
}
