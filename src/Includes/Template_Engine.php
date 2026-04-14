<?php
/**
 * Template Engine
 *
 * Centralizes schema template loading, variable building, template evaluation,
 * title normalization, state abbreviation, and cart data parsing (specs, images,
 * warranty) that was previously scattered in dms-bridge-plugin.php.
 *
 * @package Tigon\DmsConnect\Includes
 */

namespace Tigon\DmsConnect\Includes;

if (!defined('ABSPATH')) {
    exit;
}

class Template_Engine
{
    /**
     * US state abbreviation map.
     */
    private static $state_map = [
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

    /**
     * Normalize a product title: convert dashes to "In", collapse whitespace.
     *
     * @param string $title
     * @return string
     */
    public static function normalize_title(string $title): string
    {
        $normalized = str_replace(' – ', ' In ', $title);
        $normalized = str_replace(' - ', ' In ', $normalized);
        $normalized = preg_replace('/\s+/', ' ', $normalized);
        return trim($normalized);
    }

    /**
     * Convert a US state name to its two-letter abbreviation.
     *
     * @param string $state_name
     * @return string
     */
    public static function state_abbreviation(string $state_name): string
    {
        $name = trim($state_name);
        if (strlen($name) === 2) {
            return strtoupper($name);
        }
        return self::$state_map[ucwords(strtolower($name))] ?? $name;
    }

    /**
     * Load schema templates from the tigon_dms_config table.
     *
     * @return array<string,string>
     */
    public static function get_schema_templates(): array
    {
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
            $value = $wpdb->get_var($wpdb->prepare(
                "SELECT option_value FROM {$table_name} WHERE option_name = %s LIMIT 1",
                $key
            ));
            if ($value === null || $value === '') {
                $value = $default;
            }
            $templates[$key] = $value;
        }

        return $templates;
    }

    /**
     * Build a flat variable map from DMS cart data for template substitution.
     *
     * @param array $cart_data
     * @return array<string,mixed>
     */
    public static function build_template_variables(array $cart_data): array
    {
        $make     = $cart_data['cartType']['make'] ?? '';
        $model    = $cart_data['cartType']['model'] ?? '';
        $year     = $cart_data['cartType']['year'] ?? '';
        $color    = $cart_data['cartAttributes']['cartColor'] ?? '';
        $seat     = $cart_data['cartAttributes']['seatColor'] ?? '';
        $store_id = $cart_data['cartLocation']['locationId'] ?? '';

        $city      = '';
        $state     = '';
        $stateAbbr = '';

        if ($store_id && class_exists('DMS_API')) {
            $location_data = \DMS_API::get_city_and_state_by_store_id($store_id);
            $city      = $location_data['city'] ?? '';
            $state     = $location_data['state'] ?? '';
            $stateAbbr = self::state_abbreviation($state);
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
     * Evaluate a schema template ({var} / {^var}) against variables.
     *
     * {^var} applies ucwords() to the value.
     *
     * @param string $template
     * @param array  $vars
     * @param bool   $slugify  Whether to sanitize_title() the result.
     * @return string
     */
    public static function evaluate(string $template, array $vars, bool $slugify = false): string
    {
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
     * Parse DMS cart data into structured specs for product meta.
     *
     * @param array $cart_data Full DMS cart payload.
     * @return array Feature => Description pairs.
     */
    public static function parse_cart_specs(array $cart_data): array
    {
        $specs = [];

        // Cart Type
        if (!empty($cart_data['cartType'])) {
            $ct = $cart_data['cartType'];
            if (!empty($ct['make']))  $specs['Make']  = $ct['make'];
            if (!empty($ct['model'])) $specs['Model'] = $ct['model'];
            if (!empty($ct['year']))  $specs['Year']  = $ct['year'];
        }

        // Street Legal
        if (isset($cart_data['title']['isStreetLegal'])) {
            $specs['Street Legal'] = $cart_data['title']['isStreetLegal'] ? 'Fully Street Legal' : 'No';
        }

        // Cart Attributes
        if (!empty($cart_data['cartAttributes'])) {
            $a = $cart_data['cartAttributes'];
            if (!empty($a['cartColor']))     $specs['Color']         = $a['cartColor'];
            if (!empty($a['seatColor']))     $specs['Seat Color']    = $a['seatColor'];
            if (!empty($a['tireType']))      $specs['Tires']         = $a['tireType'];
            if (!empty($a['tireRimSize']))   $specs['Rims']          = $a['tireRimSize'] . '"';
            if (!empty($a['driveTrain']))    $specs['Drivetrain']    = $a['driveTrain'];
            if (!empty($a['passengers']))    $specs['Passengers']    = $a['passengers'];
            if (isset($a['hasSoundSystem']) && $a['hasSoundSystem'] !== null) {
                $specs['Sound System'] = $a['hasSoundSystem'] ? 'Yes' : 'No';
            }
            if (isset($a['isLifted']) && $a['isLifted'] !== null) {
                $specs['Lift Kit'] = $a['isLifted'] ? 'Yes' : 'No';
            }
            if (isset($a['hasHitch']) && $a['hasHitch'] !== null) {
                $specs['Receiver Hitch'] = $a['hasHitch'] ? 'Yes' : 'No';
            }
            if (isset($a['hasExtendedTop']) && $a['hasExtendedTop'] !== null) {
                $specs['Extended Top'] = $a['hasExtendedTop'] ? 'Yes' : 'No';
            }
        }

        // Battery
        if (!empty($cart_data['battery']) && is_array($cart_data['battery'])) {
            $b = $cart_data['battery'];
            if (!empty($b['type']))           $specs['Battery Type']    = $b['type'];
            if (!empty($b['brand']))          $specs['Battery Brand']   = $b['brand'];
            if (!empty($b['year']))           $specs['Battery Year']    = $b['year'];
            if (!empty($b['ampHours']))       $specs['Capacity']        = $b['ampHours'] . ' Amp Hours';
            if (!empty($b['batteryVoltage'])) $specs['Battery Voltage'] = $b['batteryVoltage'] . 'V';
            if (!empty($b['warrantyLength'])) $specs['Battery Warranty'] = $b['warrantyLength'];
        }

        // Engine (gas carts)
        if (!empty($cart_data['engine']) && is_array($cart_data['engine'])) {
            $e = $cart_data['engine'];
            if (!empty($e['make']))       $specs['Engine']     = $e['make'];
            if (!empty($e['horsepower'])) $specs['Horsepower'] = $e['horsepower'] . ' HP';
            if (!empty($e['stroke']))     $specs['Stroke']     = $e['stroke'];
        }

        // Vehicle Warranty
        if (!empty($cart_data['warrantyLength'])) {
            $specs['Warranty'] = $cart_data['warrantyLength'];
        }

        // Additional Information
        if (isset($cart_data['isElectric'])) {
            $specs['Vehicle Power'] = $cart_data['isElectric'] ? 'ELECTRIC' : 'GAS';
        }
        if (isset($cart_data['isUsed'])) {
            $specs['Vehicle Status'] = $cart_data['isUsed'] ? 'USED' : 'NEW';
        }
        if (!empty($cart_data['serialNo']))  $specs['Serial Number'] = $cart_data['serialNo'];
        if (!empty($cart_data['vinNo']))     $specs['VIN']           = $cart_data['vinNo'];
        if (!empty($cart_data['odometer']))  $specs['Odometer']      = $cart_data['odometer'];
        if (!empty($cart_data['hour']))      $specs['Hours']         = $cart_data['hour'];

        // Location
        if (!empty($cart_data['cartLocation'])) {
            $store_id = $cart_data['cartLocation']['locationId'] ?? '';
            if ($store_id && class_exists('DMS_API')) {
                $loc = \DMS_API::get_city_and_state_by_store_id($store_id);
                $parts = array_filter([$loc['city'] ?? '', $loc['state'] ?? '']);
                if (!empty($parts)) {
                    $specs['Location'] = implode(', ', $parts);
                }
            }
        }

        if (!empty($cart_data['cartType']['year'])) {
            $specs['Year of Vehicle'] = $cart_data['cartType']['year'];
        }

        return $specs;
    }

    /**
     * Extract image URLs from DMS cart data.
     *
     * The DMS API returns imageUrls in two possible shapes:
     *   - an array: ["cart123/img1.jpg", "cart123/img2.jpg"]
     *   - a comma-separated string: "cart123/img1.jpg,cart123/img2.jpg"
     *
     * Normalises both to an array of trimmed, non-empty strings.
     *
     * @param array $cart_data
     * @return array
     */
    public static function parse_cart_images(array $cart_data): array
    {
        $raw = $cart_data['imageUrls'] ?? [];
        if (is_string($raw)) {
            $raw = explode(',', $raw);
        }
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            if (!is_string($item)) continue;
            $trimmed = trim($item);
            if ($trimmed !== '') {
                $out[] = $trimmed;
            }
        }
        return $out;
    }

    /**
     * Extract warranty information from DMS cart data.
     *
     * @param array $cart_data
     * @return array
     */
    public static function parse_cart_warranty(array $cart_data): array
    {
        $warranty = [];
        if (!empty($cart_data['warrantyLength'])) {
            $warranty['warrantyLength'] = $cart_data['warrantyLength'];
        }
        if (!empty($cart_data['battery']['warrantyLength'])) {
            $warranty['batteryWarrantyLength'] = $cart_data['battery']['warrantyLength'];
        }
        return $warranty;
    }
}
