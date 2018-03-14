<?php

/*
Plugin Name: WPU Popin
Description: Add a popin on your user's first visit
Plugin URI: https://github.com/WordPressUtilities/wpupopin
Version: 0.1.0
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUPopin {
    private $plugin_version = '0.1.0';
    private $settings_values = array();
    private $settings_plugin = array();

    public function __construct() {
        add_action('plugins_loaded', array(&$this, 'load_settings'));
        add_action('wp_enqueue_scripts', array(&$this, 'load_assets_front'));
        add_action('wp_footer', array(&$this, 'load_popin_front'));
    }

    /**
     * Settings
     */
    public function load_settings() {
        $this->settings_details = array(
            'create_page' => true,
            'parent_page' => 'tools.php',
            'plugin_name' => 'WPU Popin',
            'plugin_id' => 'wpupopin',
            'option_id' => 'wpupopin_options',
            'sections' => array(
                'popin' => array(
                    'name' => __('Popin settings', 'wpupopin')
                ),
                'content' => array(
                    'name' => __('Popin content', 'wpupopin')
                )
            )
        );
        $this->settings = array(
            'display_popin' => array(
                'section' => 'popin',
                'label' => __('Popin', 'wpupopin'),
                'label_check' => __('Display a popin on first visit', 'wpupopin'),
                'type' => 'checkbox'
            ),
            'close_overlay' => array(
                'section' => 'popin',
                'label' => __('Close on overlay', 'wpupopin'),
                'label_check' => __('Close popin when clicking on overlay', 'wpupopin'),
                'type' => 'checkbox'
            ),
            'hide_default_theme' => array(
                'section' => 'content',
                'label' => __('Hide theme', 'wpupopin'),
                'label_check' => __('Hide default theme', 'wpupopin'),
                'type' => 'checkbox'
            ),
            'content_text' => array(
                'section' => 'content',
                'label' => __('Popin content', 'wpupopin'),
                'type' => 'editor'
            ),
            'button_text' => array(
                'section' => 'content',
                'label' => __('Button text', 'wpupopin'),
                'type' => 'text'
            )
        );

        include dirname(__FILE__) . '/inc/WPUBaseSettings/WPUBaseSettings.php';

        $this->settings_plugin = new \wpupopin\WPUBaseSettings($this->settings_details, $this->settings);
        $this->settings_values = apply_filters('wpupopin__settings', $this->settings_plugin->get_setting_values());
    }

    /**
     * Load frontend assets
     */
    public function load_assets_front() {
        if (!$this->settings_values['display_popin']) {
            return;
        }

        /* Load basic style */
        wp_enqueue_style('wpupopin-front', plugins_url('assets/front.css', __FILE__), array(), $this->plugin_version);
        if (!$this->settings_values['hide_default_theme']) {
            wp_enqueue_style('wpupopin-front-theme', plugins_url('assets/front-theme.css', __FILE__), array('wpupopin-front'), $this->plugin_version);
        }

        /* Load JS */
        wp_enqueue_script('wpupopin-front', plugins_url('assets/front.js', __FILE__), array(
            'jquery'
        ), $this->plugin_version, true);

        /* Add settings */
        wp_localize_script('wpupopin-front', 'wpupopin_settings', array(
            'close_overlay' => $this->settings_values['close_overlay'],
            'cookie_id' => $this->settings_details['plugin_id']
        ));

    }

    /**
     * Display popin
     */
    public function load_popin_front() {
        if (!$this->settings_values['display_popin']) {
            return;
        }
        if (!$this->settings_values['button_text']) {
            $this->settings_values['button_text'] = 'ok';
        }
        include dirname(__FILE__) . '/tpl/popin.php';
    }

}

$WPUPopin = new WPUPopin();
