<?php
/**
 * DMS Connect Product Filter — core (no Elementor dependency).
 *
 * Provides:
 *  - Filter group definitions
 *  - Server-side rendering of the filter sidebar
 *  - [dms_connect_product_filter] shortcode
 *  - pre_get_posts hook applying filter URL params to WooCommerce queries
 *  - Asset enqueue helper used by both the shortcode and the Elementor widget
 *
 * @package DMS_Bridge
 */

if (!defined('ABSPATH')) {
    exit;
}

class DMS_Connect_Product_Filter {

    const URL_PREFIX = 'dms_';

    /**
     * Filter groups — keyed by URL param suffix (full param: dms_<key>).
     */
    public static function get_filter_groups() {
        return array(
            'condition' => array(
                'label'   => __('Condition', 'tigon-dms-connect'),
                'source'  => 'pa_vehicle-status',
                'options' => array(
                    'new'  => __('New',  'tigon-dms-connect'),
                    'used' => __('Used', 'tigon-dms-connect'),
                ),
            ),
            'fuel' => array(
                'label'   => __('Fuel Type', 'tigon-dms-connect'),
                'source'  => 'pa_vehicle-power',
                'options' => array(
                    'electric' => __('Electric', 'tigon-dms-connect'),
                    'gas'      => __('Gas',      'tigon-dms-connect'),
                ),
            ),
            'street_legal' => array(
                'label'   => __('Street Legal', 'tigon-dms-connect'),
                'source'  => 'pa_street-legal',
                'options' => array(
                    'yes' => __('Yes', 'tigon-dms-connect'),
                    'no'  => __('No',  'tigon-dms-connect'),
                ),
            ),
            'lifted' => array(
                'label'    => __('Lifted', 'tigon-dms-connect'),
                'source'   => 'pa_lift-kit',
                'options'  => array(
                    'yes' => __('Yes', 'tigon-dms-connect'),
                    'no'  => __('No',  'tigon-dms-connect'),
                ),
                // pa_lift-kit term slugs are "3-inch" / "no".
                'value_map' => array(
                    'yes' => '3-inch',
                    'no'  => 'no',
                ),
            ),
            'location' => array(
                'label'  => __('Location', 'tigon-dms-connect'),
                'source' => 'location',
            ),
            'seats' => array(
                'label'    => __('Seats', 'tigon-dms-connect'),
                'source'   => 'pa_passengers',
                'options'  => array(
                    '2' => __('2 Seater', 'tigon-dms-connect'),
                    '4' => __('4 Seater', 'tigon-dms-connect'),
                    '6' => __('6 Seater', 'tigon-dms-connect'),
                    '8' => __('8 Seater', 'tigon-dms-connect'),
                ),
                'value_map' => array(
                    '2' => '2-seater',
                    '4' => '4-seater',
                    '6' => '6-seater',
                    '8' => '8-seater',
                ),
            ),
            'manufacturer' => array(
                'label'  => __('Manufacturers', 'tigon-dms-connect'),
                'source' => 'manufacturers',
            ),
            'model' => array(
                'label'  => __('Model', 'tigon-dms-connect'),
                'source' => 'models',
            ),
        );
    }

    /**
     * Render the filter sidebar.
     *
     * @param array $atts Already-normalized attributes.
     * @return string HTML
     */
    public static function render($atts = array()) {
        self::enqueue_assets();

        $defaults = array(
            'title'              => __('Shop Filter', 'tigon-dms-connect'),
            'show_title'         => 'yes',
            'show_reset'         => 'yes',
            'collapsible_mobile' => 'yes',
            'target_url'         => '',
        );
        $groups = self::get_filter_groups();
        foreach ($groups as $key => $group) {
            $defaults['show_' . $key] = 'yes';
        }
        $atts = array_merge($defaults, (array) $atts);

        $current     = self::get_active_filters();
        $form_action = !empty($atts['target_url']) ? esc_url($atts['target_url']) : esc_url(self::current_shop_url());

        ob_start();
        ?>
        <aside class="dms-cpf-sidebar<?php echo $atts['collapsible_mobile'] === 'yes' ? ' dms-cpf-collapsible' : ''; ?>"
               data-target-url="<?php echo esc_attr($atts['target_url']); ?>">
            <form class="dms-cpf-form" method="get" action="<?php echo $form_action; ?>" novalidate>
                <?php
                // Preserve non-filter query args (search, orderby, etc.).
                foreach ($_GET as $k => $v) {
                    if (strpos($k, self::URL_PREFIX) === 0) {
                        continue;
                    }
                    if (is_array($v)) {
                        foreach ($v as $vv) {
                            printf('<input type="hidden" name="%s[]" value="%s">', esc_attr($k), esc_attr($vv));
                        }
                    } else {
                        printf('<input type="hidden" name="%s" value="%s">', esc_attr($k), esc_attr($v));
                    }
                }
                ?>

                <?php if ($atts['show_title'] === 'yes' && !empty($atts['title'])): ?>
                    <button type="button" class="dms-cpf-header" aria-expanded="true">
                        <span class="dms-cpf-header-title"><?php echo esc_html($atts['title']); ?></span>
                        <span class="dms-cpf-header-toggle" aria-hidden="true"></span>
                    </button>
                <?php endif; ?>

                <div class="dms-cpf-body">
                    <?php foreach ($groups as $key => $group):
                        if ($atts['show_' . $key] !== 'yes') continue;
                        $options = self::resolve_group_options($group);
                        if (empty($options)) continue;
                        $param    = self::URL_PREFIX . $key;
                        $selected = $current[$key] ?? array();
                        ?>
                        <fieldset class="dms-cpf-group" data-filter-key="<?php echo esc_attr($key); ?>">
                            <legend class="dms-cpf-group-title"><?php echo esc_html($group['label']); ?></legend>
                            <ul class="dms-cpf-options">
                                <?php foreach ($options as $opt_value => $opt_label):
                                    $checked = in_array((string) $opt_value, $selected, true);
                                    $id = 'dms-cpf-' . sanitize_html_class($key . '-' . $opt_value);
                                    ?>
                                    <li class="dms-cpf-option">
                                        <label for="<?php echo esc_attr($id); ?>">
                                            <input
                                                type="checkbox"
                                                id="<?php echo esc_attr($id); ?>"
                                                name="<?php echo esc_attr($param); ?>[]"
                                                value="<?php echo esc_attr($opt_value); ?>"
                                                <?php checked($checked); ?>>
                                            <span class="dms-cpf-option-label"><?php echo esc_html($opt_label); ?></span>
                                        </label>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        </fieldset>
                    <?php endforeach; ?>

                    <div class="dms-cpf-actions">
                        <button type="submit" class="dms-cpf-apply"><?php esc_html_e('Apply Filters', 'tigon-dms-connect'); ?></button>
                        <?php if ($atts['show_reset'] === 'yes'): ?>
                            <a href="<?php echo $form_action; ?>" class="dms-cpf-reset"><?php esc_html_e('Reset', 'tigon-dms-connect'); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </aside>
        <?php
        return ob_get_clean();
    }

    /**
     * Build the option list (value => label) for a filter group.
     */
    private static function resolve_group_options($group) {
        if (isset($group['options']) && is_array($group['options'])) {
            return $group['options'];
        }

        $taxonomy = $group['source'];
        if (!taxonomy_exists($taxonomy)) {
            return array();
        }

        $terms = get_terms(array(
            'taxonomy'   => $taxonomy,
            'hide_empty' => true,
            'orderby'    => 'name',
            'order'      => 'ASC',
        ));

        if (is_wp_error($terms) || empty($terms)) {
            return array();
        }

        $opts = array();
        foreach ($terms as $term) {
            $opts[$term->slug] = $term->name;
        }
        return $opts;
    }

    /**
     * Active filters from $_GET, keyed by filter key.
     */
    public static function get_active_filters() {
        $active = array();
        foreach (self::get_filter_groups() as $key => $group) {
            $param = self::URL_PREFIX . $key;
            if (!isset($_GET[$param])) continue;
            $raw = $_GET[$param];
            if (is_string($raw)) {
                $raw = explode(',', $raw);
            }
            $values = array_filter(array_map('sanitize_text_field', (array) $raw));
            if (!empty($values)) {
                $active[$key] = array_values(array_unique($values));
            }
        }
        return $active;
    }

    /**
     * Resolve the form action URL for the current request.
     */
    private static function current_shop_url() {
        global $wp;

        if (function_exists('is_shop') && (is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag())) {
            if (isset($wp->request)) {
                return home_url(add_query_arg(array(), $wp->request));
            }
        }

        if (function_exists('wc_get_page_permalink')) {
            $url = wc_get_page_permalink('shop');
            if (!empty($url)) {
                return $url;
            }
        }

        return remove_query_arg(array_keys($_GET));
    }

    /**
     * Apply active filters to the main WooCommerce product query.
     */
    public static function apply_query_filters($query) {
        if (is_admin() || !$query->is_main_query()) {
            return;
        }
        if (!self::is_filterable_query($query)) {
            return;
        }

        $active = self::get_active_filters();
        if (empty($active)) {
            return;
        }

        $groups    = self::get_filter_groups();
        $tax_query = (array) $query->get('tax_query');

        foreach ($active as $key => $values) {
            if (!isset($groups[$key])) continue;
            $group    = $groups[$key];
            $taxonomy = $group['source'];

            if (!taxonomy_exists($taxonomy)) {
                continue;
            }

            // Translate label-style values (e.g. "yes"/"no") to actual term slugs.
            if (!empty($group['value_map'])) {
                $mapped = array();
                foreach ($values as $v) {
                    $mapped[] = $group['value_map'][$v] ?? $v;
                }
                $values = $mapped;
            }

            $tax_query[] = array(
                'taxonomy' => $taxonomy,
                'field'    => 'slug',
                'terms'    => $values,
                'operator' => 'IN',
            );
        }

        if (count($tax_query) > 1 && empty($tax_query['relation'])) {
            $tax_query['relation'] = 'AND';
        }

        $query->set('tax_query', $tax_query);
    }

    private static function is_filterable_query($query) {
        if ($query->get('post_type') === 'product') {
            return true;
        }
        if (function_exists('is_shop') && (is_shop() || is_product_taxonomy() || is_product_category() || is_product_tag())) {
            return true;
        }
        return false;
    }

    /**
     * Enqueue widget CSS/JS.
     */
    public static function enqueue_assets() {
        $plugin_url = defined('TIGON_DMS_PLUGIN_URL') ? TIGON_DMS_PLUGIN_URL : plugin_dir_url(dirname(__FILE__));
        $plugin_dir = defined('TIGON_DMS_PLUGIN_DIR') ? TIGON_DMS_PLUGIN_DIR : plugin_dir_path(dirname(__FILE__));
        $version    = defined('TIGON_DMS_VERSION') ? TIGON_DMS_VERSION : '1.0.0';

        $css_path = $plugin_dir . 'assets/css/dms-product-filter.css';
        wp_enqueue_style(
            'dms-product-filter',
            $plugin_url . 'assets/css/dms-product-filter.css',
            array(),
            file_exists($css_path) ? filemtime($css_path) : $version
        );

        $js_path = $plugin_dir . 'assets/js/dms-product-filter.js';
        wp_enqueue_script(
            'dms-product-filter',
            $plugin_url . 'assets/js/dms-product-filter.js',
            array(),
            file_exists($js_path) ? filemtime($js_path) : $version,
            true
        );
    }
}

/**
 * Shortcode: [dms_connect_product_filter]
 */
function dms_connect_product_filter_shortcode($atts) {
    $defaults = array(
        'title'              => __('Shop Filter', 'tigon-dms-connect'),
        'show_title'         => 'yes',
        'show_reset'         => 'yes',
        'collapsible_mobile' => 'yes',
        'target_url'         => '',
    );
    foreach (DMS_Connect_Product_Filter::get_filter_groups() as $key => $group) {
        $defaults['show_' . $key] = 'yes';
    }

    $atts = shortcode_atts($defaults, $atts, 'dms_connect_product_filter');
    return DMS_Connect_Product_Filter::render($atts);
}
add_shortcode('dms_connect_product_filter', 'dms_connect_product_filter_shortcode');

add_action('pre_get_posts', array('DMS_Connect_Product_Filter', 'apply_query_filters'));
