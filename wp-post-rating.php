<?php

/*
Plugin Name: Wp Post Rating
Plugin URI: http://romua1d.ru/wp_post_rating
Description: Powerful post rating wordpress plugin.
Version: 1.0.3.1
Author: Romua1d
Author URI: https://romua1d.ru
Text Domain: wp-post-rating
License: MIT
*/

namespace WPR_Plugin;

use WPR_Plugin\Admin\Admin;
use WPR_Plugin\Admin\Settings;

//* Don't access this file directly
defined('ABSPATH') or die();

if (!class_exists('InitRating')) {

    class InitRating
    {
        public $lang_vote = '';

        public function __construct()
        {
            // display rating

//            add_filter('the_content', [$this, 'add_rating_after_content']);
            add_action('wp_enqueue_scripts', [$this, 'include_css_js']);

            // Ajax
            add_action('wp_head', [$this, 'add_meta_nonce']);

            // Interalization
            add_action('init', [$this, 'load_plugin_text_domain']);

            // load classes
            $this->load_classes();

            // load config
            $this->config = new Config();
            $this->database = new Database($this->config);

            register_activation_hook(__FILE__, [$this->database, 'plugin_install']);

            new Settings($this->config);
            new Admin($this->config);
            new Ajax($this->config, $this->database);

            $this->position = get_option('wpr_position');

            if ($this->position == 'shortcode')
                add_shortcode('wp_rating', [$this, 'displayRating']);

            // Add settings link
//            add_filter("plugin_action_links_{$this->config->PLUGIN_NAME}", [$this, 'add_settings_link_to_plugin_list']);

            add_filter("plugin_action_links_" . plugin_basename(__FILE__), [$this, 'add_settings_link_to_plugin_list']);

            // Adding widgets
            add_action( 'widgets_init', [$this, 'wpr_load_widget']);
        }

        public function include_css_js()
        {
            wp_enqueue_style(
                'wp-post-rating',
                $this->config->PLUGIN_URL . 'assets/css/wp-post-rating.min.css',
                [],
                $this->config->PLUGIN_VERSION,
                'all'
            );

            wp_enqueue_script(
                'wp-post-rating',
                $this->config->PLUGIN_URL . 'assets/js/min/wp-post-rating.min.js',
                ['jquery'],
                $this->config->PLUGIN_VERSION,
                true
            );
        }

//        public function add_rating_after_content($content)
//        {
//
//            if (is_single() && $this->position == 'before')
//                require_once $this->config->PLUGIN_PATH . 'templates' . DIRECTORY_SEPARATOR . 'main.php';
//
//            echo do_shortcode($content);
//            print $content;
//
//            if (is_single() && $this->position == 'after')
//                require_once $this->config->PLUGIN_PATH . 'templates' . DIRECTORY_SEPARATOR . 'main.php';
//        }

        public function load_plugin_text_domain()
        {
            $locale = apply_filters('plugin_locale', get_locale(), $this->config->PLUGIN_NAME);
            if ($loaded = load_textdomain($this->config->PLUGIN_NAME, trailingslashit(WP_LANG_DIR) . $this->config->PLUGIN_NAME . DIRECTORY_SEPARATOR . $this->config->PLUGIN_NAME . '-' . $locale . '.mo')) {
                return $loaded;
            } else {
                load_plugin_textdomain($this->config->PLUGIN_NAME, FALSE, basename(dirname(__FILE__)) . '/languages/');
            }
        }

        public function load_classes()
        {
            /**
             * Functions
             * Require all PHP files in the /classes/ directory
             */
            foreach (glob(__DIR__ . "/classes/*.php") as $function) {
                require_once $function;
            }
            foreach (glob(__DIR__ . "/classes/admin/*.php") as $function) {
                require_once $function;
            }
        }

        /**
         * @return string
         */
        public function add_meta_nonce()
        {
            $ajax_nonce = wp_create_nonce($this->config->PLUGIN_NONCE_KEY);
            echo '<meta name="_wpr_nonce" content="' . $ajax_nonce . '" />';
        }

        public function add_settings_link_to_plugin_list($links)
        {
            $settings_link = '<a href="options-general.php?page=wpr-settings">'
                . __('Settings', $this->config->PLUGIN_NAME) .
                '</a>';
            array_push($links, $settings_link);
            return $links;

        }

        public function displayRating()
        {
            ob_start();
            require $this->config->PLUGIN_PATH . 'templates' . DIRECTORY_SEPARATOR . 'main.php';
            $html = ob_get_clean();

            return $html;
        }

        public function wpr_load_widget(){
            register_widget( new WPR_Widget($this->config) );
        }

    }

    $WPR_PLUGIN = new InitRating();

}

