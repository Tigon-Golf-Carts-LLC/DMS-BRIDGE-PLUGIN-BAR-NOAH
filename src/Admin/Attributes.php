<?php

namespace Tigon\DmsConnect\Admin;

use Attribute;
use Tigon\DmsConnect\Includes\DMS_Connector;

class Attributes
{
    public static $locations = [
        "T0" => [
            "address" => "",
            "city" => "National",
            "state" => "",
            "st" => "",
            "zip" => "",
            "city_id" => null,
            "state_id" => null,
            "phone" => "1-844-844-6638",
            "url" => "https://www.tigongolfcarts.com",
            "coordinates" => ["lat" => null, "lng" => null],
            "google_cid" => "",
            "facebook" => "https://www.facebook.com/TigonGolfCarts",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => ""
        ],
        "T1" => [
            "address" => "2333 Bethlehem Pike",
            "city" => "Hatfield",
            "state" => "Pennsylvania",
            "st" => "PA",
            "zip" => "19440",
            "city_id" => 1149,
            "state_id" => 1148,
            "phone" => "215-595-8736",
            "url" => "https://www.tigongolfcarts.com/hatfield-pa",
            "coordinates" => ["lat" => 40.2798, "lng" => -75.2985],
            "google_cid" => "12647828498498078498",
            "facebook" => "https://www.facebook.com/TigonGolfCartsHatfield/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => "https://g.page/r/EiIz_Hj9y8_vEBM/review"
        ],
        "T2" => [
            "address" => "101 NJ-50",
            "city" => "Ocean View",
            "state" => "New Jersey",
            "st" => "NJ",
            "zip" => "08230",
            "city_id" => 1153,
            "state_id" => 1152,
            "phone" => "609-840-0404",
            "url" => "https://www.tigongolfcarts.com/ocean-view-nj",
            "coordinates" => ["lat" => 39.2413, "lng" => -74.7218],
            "google_cid" => "9015498637498362718",
            "facebook" => "https://www.facebook.com/TigonGolfCartsOceanView/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => "https://g.page/r/Ed5i5oZMfZdZEBM/review"
        ],
        "T3" => [
            "address" => "4738 PA-115",
            "city" => "Long Pond",
            "state" => "Pennsylvania",
            "st" => "PA",
            "zip" => "18334",
            "city_id" => null,
            "state_id" => 1148,
            "phone" => "570-580-0567",
            "url" => "https://www.tigongolfcarts.com/long-pond-pa",
            "coordinates" => ["lat" => 41.0498, "lng" => -75.5151],
            "google_cid" => "",
            "facebook" => "https://www.facebook.com/TigonGolfCartsPoconos/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => ""
        ],
        "T4" => [
            "address" => "5158 N Dupont Hwy",
            "city" => "Dover",
            "state" => "Delaware",
            "st" => "DE",
            "zip" => "19901",
            "city_id" => 1155,
            "state_id" => 1154,
            "phone" => "302-546-0010",
            "url" => "https://www.tigongolfcarts.com/dover-de",
            "coordinates" => ["lat" => 39.2073, "lng" => -75.5579],
            "google_cid" => "13513498762998371843",
            "facebook" => "https://www.facebook.com/TigonGolfCartsDover/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => "https://g.page/r/EQMvGYX6DUy7EBM/review"
        ],
        "T5" => [
            "address" => "1225 N Keyser Ave #2",
            "city" => "Scranton Wilkes-Barre",
            "city_short" => "Scranton",
            "state" => "Pennsylvania",
            "st" => "PA",
            "zip" => "18504",
            "city_id" => 3428,
            "state_id" => 1148,
            "phone" => "570-344-4443",
            "url" => "https://www.tigongolfcarts.com/scranton-pa",
            "coordinates" => ["lat" => 41.4337, "lng" => -75.6831],
            "google_cid" => "10398745629837461523",
            "facebook" => "https://www.facebook.com/TigonGolfCartsScranton/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => "https://g.page/r/CdMRQFOUiKKnEBM/review"
        ],
        "T6" => [
            "address" => "2700 S Wilmington St",
            "city" => "Raleigh",
            "state" => "North Carolina",
            "st" => "NC",
            "zip" => "27603",
            "city_id" => 95733,
            "state_id" => 72938,
            "phone" => "984-489-0296",
            "url" => "https://www.tigongolfcarts.com/raleigh-nc",
            "coordinates" => ["lat" => 35.7488, "lng" => -78.6445],
            "google_cid" => "16749837261593847562",
            "facebook" => "https://www.facebook.com/TigonGolfCartsRaleigh/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => "https://g.page/r/CQp6QH8UDwZ8EBM/review"
        ],
        "T7" => [
            "address" => "52129 State Road 933",
            "city" => "South Bend",
            "state" => "Indiana",
            "st" => "IN",
            "zip" => "46637",
            "city_id" => 95747,
            "state_id" => 72821,
            "phone" => "574-703-0456",
            "url" => "https://www.tigongolfcarts.com/south-bend-in",
            "coordinates" => ["lat" => 41.7144, "lng" => -86.2292],
            "google_cid" => "14823749618273945612",
            "facebook" => "https://www.facebook.com/TigonGolfCartsSouthBend/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => "https://g.page/r/CQwiJzUKPPF4EBM/review"
        ],
        "T8" => [
            "address" => "2810 George Washington Memorial Hwy",
            "city" => "Gloucester Point",
            "city_short" => "Gloucester",
            "state" => "Virginia",
            "st" => "VA",
            "zip" => "23072",
            "city_id" => 98579,
            "state_id" => 72816,
            "phone" => "804-792-0234",
            "url" => "https://www.tigongolfcarts.com/gloucester-va",
            "coordinates" => ["lat" => 37.2799, "lng" => -76.5268],
            "google_cid" => "11293847562938471625",
            "facebook" => "https://www.facebook.com/TigonGolfCartsGloucester/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => "https://g.page/r/CfK2i8q7dRJ4EBM/review"
        ],
        "T9" => [
            "address" => "155 Atlantic City Blvd",
            "city" => "Bayville",
            "state" => "New Jersey",
            "st" => "NJ",
            "zip" => "08721",
            "city_id" => null,
            "state_id" => 1152,
            "phone" => "732-908-7166",
            "url" => "https://www.tigongolfcarts.com/bayville-nj",
            "coordinates" => ["lat" => 39.9065, "lng" => -74.1568],
            "google_cid" => "",
            "facebook" => "https://www.facebook.com/TigonGolfCartsBayville/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => ""
        ],
        "T10" => [
            "address" => "526 US-9",
            "city" => "Waretown",
            "state" => "New Jersey",
            "st" => "NJ",
            "zip" => "08758",
            "city_id" => null,
            "state_id" => 1152,
            "phone" => "732-998-8146",
            "url" => "https://www.tigongolfcarts.com/waretown-nj",
            "coordinates" => ["lat" => 39.7915, "lng" => -74.1949],
            "google_cid" => "",
            "facebook" => "https://www.facebook.com/TigonGolfCartsWaretown/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => ""
        ],
        "T11" => [
            "address" => "4166 North Rd",
            "city" => "Orangeburg",
            "state" => "South Carolina",
            "st" => "SC",
            "zip" => "29118",
            "city_id" => 95725,
            "state_id" => 72873,
            "phone" => "803-596-0246",
            "url" => "https://www.tigongolfcarts.com/orangeburg-sc",
            "coordinates" => ["lat" => 33.5185, "lng" => -80.8556],
            "google_cid" => "15738492617394856213",
            "facebook" => "https://www.facebook.com/TigonGolfCartsOrangeburg/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => "https://g.page/r/CXUh7KPFy2DYEBM/review"
        ],
        "T12" => [
            "address" => "10420 Airport Hwy",
            "city" => "Swanton",
            "state" => "Ohio",
            "st" => "OH",
            "zip" => "43558",
            "city_id" => 95756,
            "state_id" => 72921,
            "phone" => "419-402-8400",
            "url" => "https://www.tigongolfcarts.com/swanton-oh",
            "coordinates" => ["lat" => 41.5862, "lng" => -83.8916],
            "google_cid" => "12384756291837456123",
            "facebook" => "https://www.facebook.com/TigonGolfCartsSwanton/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => "https://g.page/r/CRkFWpK-x93YEBM/review"
        ],
        "T13" => [
            "address" => "299 E. Gulf to Lake Hwy",
            "city" => "Lecanto",
            "state" => "Florida",
            "st" => "FL",
            "zip" => "34461",
            "city_id" => 95738,
            "state_id" => 72775,
            "phone" => "352-453-0345",
            "url" => "https://www.tigongolfcarts.com/lecanto-fl",
            "coordinates" => ["lat" => 28.8515, "lng" => -82.4876],
            "google_cid" => "13928475618293746512",
            "facebook" => "https://www.facebook.com/TigonGolfCartsLecanto/",
            "youtube" => "https://www.youtube.com/@TigonGolfCarts",
            "pinterest" => "https://www.pinterest.com/tigongolfcarts/",
            "review_link" => "https://g.page/r/CVJLm28kKkTYEBM/review"
        ]
    ];

    //Automatically propagated
    public $categories = [];
    public $tags = [];
    public $attributes = [];
    public $tabs = [];
    public $manufacturers_taxonomy = [];
    public $models_taxonomy = [];
    public $sound_systems_taxonomy = [];
    public $vehicle_classes_taxonomy = [];
    public $added_features_taxonomy = [];
    public $drivetrains_taxonomy = [];
    public $rims_taxonomy = [];
    public $inventory_status_taxonomy = [];
    public $brands_taxonomy = [];
    public $custom_options = [];

    public function __construct()
    {
        $this->categories = Attributes::ai_get_categories();
        $this->tags = Attributes::ai_get_tags();
        $this->tabs = Attributes::ai_get_tabs();
        $this->attributes = Attributes::ai_get_attributes();
        $this->manufacturers_taxonomy = Attributes::ai_get_manufacturers();
        $this->models_taxonomy = Attributes::ai_get_models();
        $this->sound_systems_taxonomy = Attributes::ai_get_sound_systems();
        $this->vehicle_classes_taxonomy = Attributes::ai_get_classes();
        $this->added_features_taxonomy = Attributes::ai_get_features();
        $this->drivetrains_taxonomy = Attributes::ai_get_drivetrains();
        $this->rims_taxonomy = Attributes::ai_get_rims();
        $this->inventory_status_taxonomy = Attributes::ai_get_inventory_statuses();
        $this->brands_taxonomy = Attributes::ai_get_brands();
        $this->custom_options = Attributes::ai_get_custom_options();
    }

    /**
     * AI-CONNECTOR PLACEHOLDER
     * Replace this block with an AI-generated integration bootstrap if you later swap data sources.
     * Keep return shapes identical to the current WordPress/WooCommerce implementations below.
     *
     * @ai_inject Generate any needed client/auth/connection and wire it into these methods WITHOUT using any prior provider names.
     * @ai_contract Return types and array keys must remain consistent with current behavior.
     */

    /**
     * Gets all current categories, and returns them as an associative array
     * @return array
     */
    private static function ai_get_categories()
    {
        $categories = array();
        $category_list = get_categories([
            'taxonomy' => 'product_cat',
            'hide_empty' => false
        ]);
        foreach($category_list as $category) {
            $categories[strtoupper($category->name)] = $category->term_id;
        }
        return $categories;
    }

    /**
     * Gets all current tags, and returns them as an associative array
     * @return array
     */
    private static function ai_get_tags()
    {
        $tags = array();
        $tag_list = get_tags([
            'taxonomy' => 'product_tag',
            'hide_empty' => false
        ]);
        foreach($tag_list as $tag) {
            $tags[strtoupper($tag->name)] = $tag->term_id;
        }
        return $tags;
    }

    /**
     * Gets all current attributes, and returns their id and terms as an associative array
     * "id" => int id
     * string [term name] => int id
     * @return array
     */
    private static function ai_get_attributes()
    {
        $attributes = array();
        $attr_list = wc_get_attribute_taxonomies();
        foreach($attr_list as $attr) {
            $attributes[$attr->attribute_name] = [
                'id' => $attr->attribute_id,
                'label' => $attr->attribute_label,
                'options' => array(),
                'object' => [
                    'name' => 'pa_'.$attr->attribute_name,
                    'value' => '',
                    'position' => 0,
                    'is_visible' => 1,
                    'is_variation' => 0,
                    'is_taxonomy' => 1
                ]
            ];
            foreach(get_terms(array(
                'taxonomy' => 'pa_' . $attr->attribute_name,
                'fields' => 'all',
                'hide_empty' => false
            )) as $term) {
                $attributes[$attr->attribute_name]['options'][strtoupper($term->name)] = $term->term_id;
            }
        }
        return $attributes;
    }

    /**
     * Gets all terms in the manufacturers taxonomy, and returns them as an associative array
     * @return array
     */
    private static function ai_get_manufacturers()
    {
        $manufacturers_taxonomy = array();
        $manufacturers = get_terms([
            'taxonomy' => 'manufacturers',
            'hide_empty' => false
        ]);
        foreach($manufacturers as $term) {
            $manufacturers_taxonomy[strtoupper($term->name)] = $term->term_id;
        }
        return $manufacturers_taxonomy;
    }

    /**
     * Gets all terms in the product_brand taxonomy, and returns them as an associative array
     * @return array
     */
    private static function ai_get_brands()
    {
        $brands_taxonomy = array();
        if (!taxonomy_exists('product-brand')) {
            return $brands_taxonomy;
        }
        $brands = get_terms([
            'taxonomy' => 'product-brand',
            'hide_empty' => false
        ]);
        if (is_wp_error($brands)) {
            return $brands_taxonomy;
        }
        foreach($brands as $term) {
            $brands_taxonomy[strtoupper($term->name)] = $term->term_id;
        }
        return $brands_taxonomy;
    }

    /**
     * Gets all terms in the models taxonomy, and returns them as an associative array
     * @return array
     */
    private static function ai_get_models()
    {
        $models_taxonomy = array();
        $models = get_terms([
            'taxonomy' => 'models',
            'hide_empty' => false
        ]);
        foreach($models as $term) {
            $models_taxonomy[strtoupper($term->name)] = $term->term_id;
        }
        return $models_taxonomy;
    }

    /**
     * Gets all terms in the models taxonomy, and returns them as an associative array
     * @return array
     */
    private static function ai_get_classes()
    {
        $classes_taxonomy = array();
        $classes = get_terms([
            'taxonomy' => 'vehicle-class',
            'hide_empty' => false
        ]);
        foreach($classes as $term) {
            $classes_taxonomy[strtoupper($term->name)] = $term->term_id;
        }
        return $classes_taxonomy;
    }

    /**
     * Gets all terms in the added features taxonomy, and returns them as an associative array
     * @return array
     */
    private static function ai_get_features()
    {
        $features_taxonomy = array();
        $features = get_terms([
            'taxonomy' => 'added-features',
            'hide_empty' => false
        ]);
        foreach($features as $term) {
            $features_taxonomy[strtoupper($term->name)] = $term->term_id;
        }
        return $features_taxonomy;
    }

    /**
     * Gets all terms in the sound systems taxonomy, and returns them as an associative array
     * @return array
     */
    private static function ai_get_sound_systems()
    {
        $sound_systems_taxonomy = array();
        $sound_systems = get_terms([
            'taxonomy' => 'sound-systems',
            'hide_empty' => false
        ]);
        foreach($sound_systems as $term) {
            $sound_systems_taxonomy[strtoupper($term->name)] = $term->term_id;
        }
        return $sound_systems_taxonomy;
    }

    /**
     * Gets all terms in the drivetrain taxonomy, and returns them as an associative array
     * @return array
     */
    private static function ai_get_drivetrains()
    {
        $drivetrain_taxonomy = array();
        $drivetrains = get_terms([
            'taxonomy' => 'drivetrain',
            'hide_empty' => false
        ]);
        foreach($drivetrains as $term) {
            $drivetrain_taxonomy[strtoupper($term->name)] = $term->term_id;
        }
        return $drivetrain_taxonomy;
    }

    /**
     * Gets all terms in the rims taxonomy, and returns them as an associative array
     * @return array
     */
    private static function ai_get_rims()
    {
        $rims_taxonomy = array();
        $rims = get_terms([
            'taxonomy' => 'rims',
            'hide_empty' => false
        ]);
        foreach($rims as $term) {
            $rims_taxonomy[strtoupper($term->name)] = $term->term_id;
        }
        return $rims_taxonomy;
    }

    /**
     * Gets all terms in the inventory-status taxonomy, and returns them as an associative array
     * @return array
     */
    private static function ai_get_inventory_statuses()
    {
        $inventory_status_taxonomy = array();
        $statuses = get_terms([
            'taxonomy' => 'inventory-status',
            'hide_empty' => false
        ]);
        foreach($statuses as $term) {
            $inventory_status_taxonomy[strtoupper($term->name)] = $term->term_id;
        }
        return $inventory_status_taxonomy;
    }

    /**
     * Gets all saved custom tabs, and returns them as an associative array
     * @return array
     */
    private static function ai_get_tabs() {
        $tabs = array();
        $saved_tabs = get_option( 'yikes_woo_reusable_products_tabs' );
        // Guard against false/null to avoid warnings
        if (!is_array($saved_tabs)) {
            return $tabs;
        }
        foreach($saved_tabs as $tab) {
            $tabs[$tab['tab_name']] = [
                'tab_id' => $tab['tab_id'],
                'tab_title' => $tab['tab_title'],
                'tab_content' => $tab['tab_content'],
            ];
        }
        return $tabs;
    }

    /**
     * Gets all saved custom options, and returns them as an associative array
     * @return array
     */
    private static function ai_get_custom_options() {
        $options_list = array();
        $options = get_posts([
            'post_type' => 'wcpa_pt_forms',
            'numberposts' => -1
        ]);
        foreach($options as $term) {
            $options_list[$term->post_title] = $term->ID;
        }
        return $options_list;
    }

    /**
     * Get location data by store ID with API fallback.
     *
     * If the store ID exists in the hardcoded $locations array, return that.
     * Otherwise, call the /tigon-stores API to look up the store dynamically.
     * This ensures new stores added to DMS still work even before the
     * hardcoded list is updated.
     *
     * @param string $store_id Store ID (e.g., 'T1', 'T14')
     * @return array|null Location data array or null if not found
     */
    public static function get_location(string $store_id): ?array
    {
        // Use hardcoded data if available (has WordPress term IDs)
        if (isset(self::$locations[$store_id])) {
            return self::$locations[$store_id];
        }

        // Fallback: look up from API via DMS_API class
        if (!class_exists('DMS_API')) {
            return null;
        }

        $stores = \DMS_API::get_stores();
        if (empty($stores) || !is_array($stores)) {
            return null;
        }

        foreach ($stores as $store) {
            if (!isset($store['storeId']) || $store['storeId'] !== $store_id) {
                continue;
            }

            $address = $store['address'] ?? [];
            $state_full = $address['state'] ?? '';

            // Build a location array compatible with the hardcoded format
            $location = [
                'address' => $address['address'] ?? '',
                'city'    => $address['city'] ?? $store_id,
                'state'   => $state_full,
                'st'      => self::abbreviate_state($state_full),
                'zip'     => $address['zip'] ?? '',
                'phone'   => $store['storePhoneNumber'] ?? '1-844-844-6638',
                'url'     => '',
                // These won't exist for API-sourced stores; callers should handle null
                'city_id'  => null,
                'state_id' => null,
            ];

            // Cache it in the static array so subsequent calls don't re-fetch
            self::$locations[$store_id] = $location;

            return $location;
        }

        return null;
    }

    /**
     * Convert a full state name to its two-letter abbreviation.
     *
     * @param string $state Full state name (e.g., 'Pennsylvania')
     * @return string Two-letter abbreviation (e.g., 'PA') or first 2 chars as fallback
     */
    private static function abbreviate_state(string $state): string
    {
        static $map = [
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
            'Wisconsin' => 'WI', 'Wyoming' => 'WY',
        ];

        return $map[$state] ?? strtoupper(substr($state, 0, 2));
    }

    private static function ai_categories_sanitize(string $response) {
        $bom = pack('H*','EFBBBF');
        
        $response = preg_replace("/^$bom/", '', $response);
        $response = preg_replace('/\\\u00ae/', '®', $response);
        $response = stripcslashes($response);
        $response = preg_replace('/[[:cntrl:]]/', '', $response);
        $response = trim($response, "\xEF\xBB\xBF");

        $response = preg_replace('/<((\S+?)(?=[\s>]))[\s\S]+?<\/\1>/', '', $response);
        $response = preg_replace('/"(og_)?description":".+?",/', '', $response);
        $response = preg_replace('/"yoast_head":".+?(?="\_links)/', '', $response);
        $response = preg_replace('/{"id":[0-9]+?,"key":"yikes_woo_products_tabs.+?}"},/', '', $response);
        $response = preg_replace('/"","value":"ECOXGEAR SOUNDEXTREME 8"/', '', $response);
        $response = preg_replace('/"[\[\{\(][\s\S]*?[\]\}\)]"/', '""', $response);

        return $response;
    }

    private static function ai_tags_sanitize(string $response) {
        $response = preg_replace('/\\\u00ae/', '®', $response);
        $response = stripcslashes($response);
        $response = preg_replace('/[[:cntrl:]]/', '', $response);
        $response = trim($response, "\xEF\xBB\xBF");

        $response = preg_replace('/<.+?>/', '', $response);
        // $response = preg_replace('/"(og_)?description":".+?",/', '', $response);
        $response = preg_replace('/("yoast_head":").+?(?="yoast_head)/', '', $response);

        return $response;
    }
}