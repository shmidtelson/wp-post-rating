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

//* Don't access this file directly
defined('ABSPATH') or die();

if (!class_exists('InitRating')) {

    class InitRating
    {
        private $PLUGIN_VERSION = '1.0';
        private $PLUGIN_PATH = '';
        private $PLUGIN_URL = '';

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
            $this->PLUGIN_PATH = plugin_dir_path(__FILE__);
            $this->PLUGIN_URL = plugin_dir_url(__FILE__);

            add_filter('the_content', [$this, 'add_rating_after_content']);
            add_action('wp_enqueue_scripts', [$this, 'include_css_js']);
        }

        public function include_css_js()
        {
            wp_enqueue_style(
                'wp-post-rating',
                $this->PLUGIN_URL . 'assets/css/wp-post-rating.min.css',
                [],
                $this->PLUGIN_VERSION,
                'all'
            );

            wp_enqueue_script(
                'wp-post-rating',
                $this->PLUGIN_URL . 'assets/js/wp-post-rating.min.js',
                ['jquery'],
                $this->PLUGIN_VERSION,
                true
            );
        }

        public function add_rating_after_content($content)
        {
            $custom_content = $content;
            $custom_content .= $this->content;
            return $custom_content;
        }
    }

    new InitRating();

}


