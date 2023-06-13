<?php

/*
Plugin Name: WPU Popin
Description: Display a popin on your user's first visit and more
Plugin URI: https://github.com/WordPressUtilities/wpupopin
Update URI: https://github.com/WordPressUtilities/wpupopin
Version: 0.8.0
Author: Darklg
Author URI: http://darklg.me/
Text Domain: wpupopin
Domain Path: /lang
Requires at least: 5.9
Requires PHP: 8.0
License: MIT License
License URI: http://opensource.org/licenses/MIT
*/

class WPUPopin {
    public $plugin_description;
    public $settings_details;
    public $settings;
    public $settings_update;
    private $plugin_version = '0.8.0';
    private $settings_values = array();
    private $settings_plugin = array();

    private $check_values = array(
        'display_after_n_pages',
        'display_after_n_clicks',
        'display_after_n_seconds',
        'display_after_n_pixels'
    );

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
        $lang_dir = dirname(plugin_basename(__FILE__)) . '/lang/';
        if (!load_plugin_textdomain('wpupopin', false, $lang_dir)) {
            load_muplugin_textdomain('wpupopin', $lang_dir);
        }
        $this->plugin_description = __('Display a popin on your user’s first visit and more', 'wpupopin');
    }

    /**
     * Settings
     */
    public function load_settings() {
        $this->settings_details = array(
            'create_page' => true,
            'plugin_basename' => plugin_basename(__FILE__),
            'plugin_name' => 'WPU Popin',
            'plugin_id' => 'wpupopin',
            'user_cap' => 'edit_others_pages',
            'option_id' => 'wpupopin_options',
            'sections' => array(
                'popin' => array(
                    'name' => __('Popin settings', 'wpupopin')
                ),
                'behavior' => array(
                    'name' => __('Behavior', 'wpupopin')
                ),
                'conditions' => array(
                    'name' => __('Conditions', 'wpupopin')
                ),
                'content' => array(
                    'name' => __('Popin content', 'wpupopin')
                ),
                'button' => array(
                    'name' => __('Buttons', 'wpupopin')
                )
            )
        );
        $this->settings = array(
            'display_popin' => array(
                'section' => 'popin',
                'label' => __('Display popin', 'wpupopin'),
                'label_check' => __('Display a popin on first visit', 'wpupopin'),
                'type' => 'checkbox',
                'help' => __('If this setting is disabled, the popin will never appear.', 'wpupopin')
            ),
            'cookie_id' => array(
                'section' => 'popin',
                'label' => __('ID Cookie', 'wpupopin'),
                'regex' => '/^([a-z0-9]+)$/',
                'help' => __('Changing cookie ID allow users to see the new content of a popin.<br />Use only lowercase letters and numbers.', 'wpupopin')
            ),
            'cookie_duration' => array(
                'section' => 'popin',
                'default' => '30',
                'label' => __('Cookie duration', 'wpupopin'),
                'help' => __('Number of days until user sees this popin again.', 'wpupopin')
            ),
            'close_overlay' => array(
                'section' => 'behavior',
                'default' => '1',
                'label' => __('Close on overlay', 'wpupopin'),
                'label_check' => __('Close popin when clicking on overlay', 'wpupopin'),
                'type' => 'checkbox'
            ),
            'close_echap' => array(
                'section' => 'behavior',
                'default' => '1',
                'label' => __('Close on echap', 'wpupopin'),
                'label_check' => __('Close popin when pressing echap key', 'wpupopin'),
                'type' => 'checkbox'
            ),
            'disable_loggedin' => array(
                'section' => 'conditions',
                'label' => __('Disable if loggedin', 'wpupopin'),
                'label_check' => __('Loggedin users will not see the popin.', 'wpupopin'),
                'type' => 'checkbox'
            ),
            'display_after_n_clicks' => array(
                'section' => 'conditions',
                'default' => '0',
                'label' => __('Display after n clicks', 'wpupopin'),
                'help' => __('Wait until the user has clicked n times anywhere on the page to display the popin.', 'wpupopin')
            ),
            'display_after_n_seconds' => array(
                'section' => 'conditions',
                'default' => '0',
                'label' => __('Display after n seconds', 'wpupopin'),
                'help' => __('Wait until the user has been at least n seconds on your website.', 'wpupopin')
            ),
            'display_after_n_pixels' => array(
                'section' => 'conditions',
                'default' => '0',
                'label' => __('Display after n pixels', 'wpupopin'),
                'help' => __('Wait until the user has scrolled at least n pixels on a page of your website.', 'wpupopin')
            ),
            'display_after_n_pages' => array(
                'section' => 'conditions',
                'default' => '0',
                'label' => __('Display after n viewed pages', 'wpupopin'),
                'help' => __('Wait until the user has viewed at least n pages of your website in the current session.', 'wpupopin')
            ),
            'hide_default_theme' => array(
                'section' => 'content',
                'label' => __('Hide theme', 'wpupopin'),
                'label_check' => __('Disable default CSS', 'wpupopin'),
                'type' => 'checkbox'
            ),
            'content_text' => array(
                'section' => 'content',
                'label' => __('Popin content', 'wpupopin'),
                'default' => __('Popin content', 'wpupopin'),
                'type' => 'editor',
                'lang' => 1
            ),
            'close_button_hidden' => array(
                'section' => 'button',
                'label' => __('Hide X button', 'wpupopin'),
                'label_check' => __('Hide X button', 'wpupopin'),
                'type' => 'checkbox'
            ),
            'button_hidden' => array(
                'section' => 'button',
                'label' => __('Hide Main button', 'wpupopin'),
                'label_check' => __('Hide Main button', 'wpupopin'),
                'type' => 'checkbox'
            ),
            'button_text' => array(
                'section' => 'button',
                'label' => __('Main button text', 'wpupopin'),
                'default' => __('Main button text', 'wpupopin'),
                'type' => 'text',
                'lang' => 1
            )
        );

        include dirname(__FILE__) . '/inc/WPUBaseSettings/WPUBaseSettings.php';
        include dirname(__FILE__) . '/inc/WPUBaseUpdate/WPUBaseUpdate.php';

        $this->settings_update = new \wpupopin\WPUBaseUpdate('WordPressUtilities', 'wpupopin', $this->plugin_version);
        $this->settings_plugin = new \wpupopin\WPUBaseSettings($this->settings_details, $this->settings);
        $this->settings_values = apply_filters('wpupopin__settings', $this->settings_plugin->get_setting_values());

        /* Default */
        if (!isset($this->settings_values['button_text']) || !$this->settings_values['button_text'] || empty($this->settings_values['button_text'])) {
            $this->settings_values['button_text'] = __('Hide', 'wpupopin');
        }
        if (!isset($this->settings_values['cookie_duration']) || !$this->settings_values['cookie_duration'] || !ctype_digit($this->settings_values['cookie_duration'])) {
            $this->settings_values['cookie_duration'] = 30;
        }

        /* Default numeric values */
        foreach ($this->check_values as $val) {
            if (!isset($this->settings_values[$val]) || !$this->settings_values[$val] || !ctype_digit($this->settings_values[$val])) {
                $this->settings_values[$val] = 0;
            }
        }
    }

    /**
     * Load frontend assets
     */
    public function load_assets_front() {
        if (!$this->should_display_popin()) {
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

        $values = array(
            'cookie_duration' => $this->settings_values['cookie_duration'],
            'close_overlay' => $this->settings_values['close_overlay'],
            'close_echap' => $this->settings_values['close_echap'],
            'cookie_id' => $this->settings_details['plugin_id'] . $this->settings_values['cookie_id']
        );

        foreach ($this->check_values as $val) {
            $values[$val] = $this->settings_values[$val];
        }

        /* Add settings */
        wp_localize_script('wpupopin-front', 'wpupopin_settings', $values);

    }

    /**
     * Display popin
     */
    public function load_popin_front() {
        if (!$this->should_display_popin()) {
            return;
        }
        $override_content = apply_filters('wpupopin__override_content', '', $this->settings_values);
        if (!empty($override_content)) {
            echo $override_content;
            return;
        }

        /* Filters for content */
        add_filter('wpupopin_the_content', 'wptexturize');
        add_filter('wpupopin_the_content', 'convert_smilies');
        add_filter('wpupopin_the_content', 'convert_chars');
        add_filter('wpupopin_the_content', 'wpautop');
        add_filter('wpupopin_the_content', 'shortcode_unautop');
        add_filter('wpupopin_the_content', 'do_shortcode');

        include dirname(__FILE__) . '/tpl/popin.php';
    }

    public function should_display_popin() {
        /* Plugin is disabled */
        if (!$this->settings_values['display_popin']) {
            return false;
        }
        if ($this->settings_values['disable_loggedin'] && is_user_logged_in()) {
            return false;
        }
        return apply_filters('wpupopin__should_display_popin', true);
    }

}

$WPUPopin = new WPUPopin();
