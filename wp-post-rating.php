<?php

/*
Plugin Name: Wp Post Rating
Plugin URI: http://romua1d.ru/wp_post_rating
Description: Powerful post rating wordpress plugin.
Version: 1.0
Author: Romua1d
Author URI: https://romua1d.ru
Text Domain: wp-post-rating
License: GPL 2.0
*/

namespace WPR_Plugin;

//* Don't access this file directly
use WPR_Plugin\Admin\Admin;

defined('ABSPATH') or die();

if (!class_exists('InitRating')) {

    class InitRating
    {
        public $lang_vote = '';

        public function __construct()
        {
            // load classes
            $this->load_classes();

            // load config
            $this->config = new Config();
            $this->database = new Database();

            new Admin();

            // display rating
            add_filter('the_content', [$this, 'add_rating_after_content']);
            add_action('wp_enqueue_scripts', [$this, 'include_css_js']);

            register_activation_hook(__FILE__, [$this->database, 'plugin_install']);

            // Interalization
            add_action('init', [$this, 'load_plugin_text_domain']);
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
                $this->config->PLUGIN_URL . 'assets/js/wp-post-rating.min.js',
                ['jquery'],
                $this->config->PLUGIN_VERSION,
                true
            );
        }

        public function add_rating_after_content($content)
        {
            $custom_content = $content;
            $custom_content .= load_template($this->config->PLUGIN_PATH . 'templates' . DIRECTORY_SEPARATOR . 'main.php');
            return $custom_content;
        }

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
            require_once 'classes' . DIRECTORY_SEPARATOR . 'Config.php';
            require_once 'classes' . DIRECTORY_SEPARATOR . 'Database.php';
            require_once 'classes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'Admin.php';
            require_once 'classes' . DIRECTORY_SEPARATOR . 'admin' . DIRECTORY_SEPARATOR . 'RatingsList.php';
        }
    }

    new InitRating();

}
