<?php
/**
 * Elementor Widget: DMS Connect Product Filter
 *
 * Thin wrapper around DMS_Connect_Product_Filter — registered only when
 * Elementor is loaded so the parent class is guaranteed to exist.
 *
 * @package DMS_Bridge
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('\Elementor\Widget_Base')) {
    return;
}

class DMS_Connect_Product_Filter_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'dms_connect_product_filter';
    }

    public function get_title() {
        return __('DMS Connect Product Filter', 'tigon-dms-connect');
    }

    public function get_icon() {
        return 'eicon-filter';
    }

    public function get_categories() {
        return array('general');
    }

    public function get_keywords() {
        return array('dms', 'filter', 'shop', 'woocommerce', 'sidebar', 'product');
    }

    protected function register_controls() {
        $this->start_controls_section(
            'content_section',
            array(
                'label' => __('Filter Settings', 'tigon-dms-connect'),
                'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
            )
        );

        $this->add_control(
            'title',
            array(
                'label'   => __('Title', 'tigon-dms-connect'),
                'type'    => \Elementor\Controls_Manager::TEXT,
                'default' => __('Shop Filter', 'tigon-dms-connect'),
            )
        );

        $this->add_control(
            'show_title',
            array(
                'label'        => __('Show Title', 'tigon-dms-connect'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'show_reset',
            array(
                'label'        => __('Show Reset Button', 'tigon-dms-connect'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'collapsible_mobile',
            array(
                'label'        => __('Collapsible on Mobile', 'tigon-dms-connect'),
                'type'         => \Elementor\Controls_Manager::SWITCHER,
                'return_value' => 'yes',
                'default'      => 'yes',
            )
        );

        $this->add_control(
            'target_url',
            array(
                'label'       => __('Shop URL', 'tigon-dms-connect'),
                'type'        => \Elementor\Controls_Manager::TEXT,
                'description' => __('Leave empty to filter the current page. Otherwise filters submit to this URL.', 'tigon-dms-connect'),
                'default'     => '',
            )
        );

        foreach (DMS_Connect_Product_Filter::get_filter_groups() as $key => $group) {
            $this->add_control(
                'show_' . $key,
                array(
                    'label'        => sprintf(__('Show: %s', 'tigon-dms-connect'), $group['label']),
                    'type'         => \Elementor\Controls_Manager::SWITCHER,
                    'return_value' => 'yes',
                    'default'      => 'yes',
                )
            );
        }

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();

        $atts = array(
            'title'              => $settings['title'] ?? '',
            'show_title'         => ($settings['show_title'] ?? 'yes') === 'yes' ? 'yes' : 'no',
            'show_reset'         => ($settings['show_reset'] ?? 'yes') === 'yes' ? 'yes' : 'no',
            'collapsible_mobile' => ($settings['collapsible_mobile'] ?? 'yes') === 'yes' ? 'yes' : 'no',
            'target_url'         => $settings['target_url'] ?? '',
        );

        foreach (DMS_Connect_Product_Filter::get_filter_groups() as $key => $group) {
            $atts['show_' . $key] = (($settings['show_' . $key] ?? 'yes') === 'yes') ? 'yes' : 'no';
        }

        echo DMS_Connect_Product_Filter::render($atts);
    }

    protected function _print_content() {
        $this->render();
    }
}
