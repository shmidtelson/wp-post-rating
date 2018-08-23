<?php

/*
Plugin Name: Wp Post Rating
Plugin URI: http://romua1d.ru/wp_post_rating
Description: Powerful post rating wordpress plugin.
Version: 1.0
Author: Romua1d
Author URI: https://romua1d.ru
License: GPL 2.0
*/
namespace WPR_Plugin;

//* Don't access this file directly
defined('ABSPATH') or die();

if (!class_exists('InitRating')) {

    class InitRating
    {

        public $content = '
<div class="wpr-wrapp">
    <div class="wpr-rating">
        <span class="icon-star" data-value="5"></span>
        <span class="icon-star" data-value="4"></span>
        <span class="icon-star" data-value="3"></span>
        <span class="icon-star" data-value="2"></span>
        <span class="icon-star" data-value="1"></span>
    </div>
    <div class="wpr-info-container">
        <span>Голосовать (0 голосов)</span>
    </div>
</div>
';

        public function __construct()
        {
            // load classes
            $this->load_classes();

            add_filter('the_content', [$this, 'add_rating_after_content']);
            add_action('wp_enqueue_scripts', [$this, 'include_css_js']);

            // Interalization
//            add_action( 'plugins_loaded', [$this, 'load_plugin_text_domain'] );
        }

        public function include_css_js()
        {
            wp_enqueue_style(
                'wp-post-rating',
                Config::$PLUGIN_URL . 'assets/css/wp-post-rating.min.css',
                [],
                Config::$PLUGIN_VERSION,
                'all'
            );

            wp_enqueue_script(
                'wp-post-rating',
                Config::$PLUGIN_URL . 'assets/js/wp-post-rating.min.js',
                ['jquery'],
                Config::$PLUGIN_VERSION,
                true
            );
        }

        public function add_rating_after_content($content)
        {
            $custom_content = $content;
            $custom_content .= $this->content;
            $custom_content .= Config::$PLUGIN_TABLE_NAME;
            $custom_content .= Config::$PLUGIN_URL;
            return $custom_content;
        }

        public function load_plugin_text_domain(){
            load_plugin_textdomain( Config::$PLUGIN_NAME, false, Config::$PLUGIN_PATH . '/languages/');
        }

        public function load_classes(){
            require_once 'classes/Config.php';
        }
    }

    new InitRating();

}


