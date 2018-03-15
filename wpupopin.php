<?php

/*
Plugin Name: WPU Popin
Description: Add a popin on your user's first visit
Plugin URI: https://github.com/WordPressUtilities/wpupopin
Version: 0.2.1
Author: Darklg
Author URI: http://darklg.me/
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUPopin {
    private $plugin_version = '0.2.1';
    private $settings_values = array();
    private $settings_plugin = array();

    public function __construct() {
        add_action('plugins_loaded', array(&$this, 'load_translation'));
        add_action('plugins_loaded', array(&$this, 'load_settings'));
        add_action('wp_enqueue_scripts', array(&$this, 'load_assets_front'));
        add_action('wp_footer', array(&$this, 'load_popin_front'));
    }

    /**
     * Translation
     */
    public function load_translation() {
        load_plugin_textdomain('wpupopin', false, dirname(plugin_basename(__FILE__)) . '/lang/');
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
            'close_echap' => array(
                'section' => 'popin',
                'label' => __('Close on echap', 'wpupopin'),
                'label_check' => __('Close popin when pressing echap key', 'wpupopin'),
                'type' => 'checkbox'
            ),
            'hide_default_theme' => array(
                'section' => 'content',
                'label' => __('Hide theme', 'wpupopin'),
                'label_check' => __('Hide default theme', 'wpupopin'),
                'type' => 'checkbox'
            ),
            'cookie_id' => array(
                'section' => 'content',
                'label' => __('ID Cookie', 'wpupopin'),
                'regex' => '/^([a-z0-9]+)$/',
                'help' => __('Changing cookie ID allow users to see the new content of a popin.<br />Use only lowercase letters and numbers.', 'wpupopin')
            ),
            'cookie_duration' => array(
                'section' => 'content',
                'label' => __('Cookie duration', 'wpupopin'),
                'help' => __('Number of days until user sees this popin again.', 'wpupopin')
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

        /* Default */
        if (!$this->settings_values['button_text'] || empty($this->settings_values['button_text'])) {
            $this->settings_values['button_text'] = __('Hide', 'wpupopin');
        }
        if (!$this->settings_values['cookie_duration'] || !ctype_digit($this->settings_values['cookie_duration'])) {
            $this->settings_values['cookie_duration'] = 30;
        }
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
            'cookie_duration' => $this->settings_values['cookie_duration'],
            'close_overlay' => $this->settings_values['close_overlay'],
            'close_echap' => $this->settings_values['close_echap'],
            'cookie_id' => $this->settings_details['plugin_id'].$this->settings_values['cookie_id']
        ));

    }

    /**
     * Display popin
     */
    public function load_popin_front() {
        if (!$this->settings_values['display_popin']) {
            return;
        }
        $override_content = apply_filters('wpupopin__override_content', '', $this->settings_values);
        if (!empty($override_content)) {
            echo $override_content;
            return;
        }
        include dirname(__FILE__) . '/tpl/popin.php';
    }

}

$WPUPopin = new WPUPopin();
