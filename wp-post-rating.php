<?php
/*
Plugin Name: Wp Post Rating
Plugin URI: http://romua1d.ru/wp_post_rating
Description: Powerful post rating wordpress plugin.
Version: 1.0.5
Author: Romua1d
Author URI: https://romua1d.ru
Text Domain: wp-post-rating
License: MIT
*/

//* Don't access this file directly
defined('ABSPATH') or die();

require_once 'vendor/autoload.php';

use WPR\Service\ConfigService;
use WPR\Service\ScriptsService;
use WPR\Service\DocumentService;
use WPR\Service\TranslateService;
use WPR\Service\MaintenanceService;

/**
 * Container create
 */
$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions([
    ConfigService::class => \DI\create( ConfigService::class),
    ScriptsService::class => \DI\create(ScriptsService::class),
    DocumentService::class => \DI\create(DocumentService::class),
    TranslateService::class => \DI\create(TranslateService::class),
    MaintenanceService::class => \DI\create(MaintenanceService::class),
]);
$containerBuilder->build();

/**
 * Wordpress hooks
 */
add_action('wp_enqueue_scripts', [new ScriptsService(), 'initScripts']);
add_action('wp_head', [new DocumentService(), 'addNonceToHead']);
add_action('init', [new TranslateService(), 'loadPluginTextDomain']);

register_activation_hook(__FILE__, [$this->database, 'plugin_install']);


class RatingDisplay
{
    public $position = 'shortcode';
    public $wprStarsMainColor;
    public $wprStarsSecondColor;
    public $wprStarsTextColor;
    public $wprStarsTextBackgroundColor;

    public function __construct()
    {
        // load classes
        $this->load_classes();

        // load config
        $this->config = new Config();
        $this->database = new Database($this->config);



        new Settings($this->config);
        new Admin($this->config);
        new Ajax($this->config, $this->database);

        $this->position = get_option('wpr_position');
        $this->wprStarsMainColor = get_option('wpr_stars_main_color');
        $this->wprStarsSecondColor = get_option('wpr_stars_second_color');
        $this->wprStarsTextColor = get_option('wpr_stars_text_color');
        $this->wprStarsTextBackgroundColor = get_option('wpr_stars_text_background_color');

        if ($this->position == 'shortcode') {
            add_shortcode('wp_rating', [$this, 'displayRating']);
        }

        // Add settings link
        add_filter("plugin_action_links_" . plugin_basename(__FILE__), [$this, 'add_settings_link_to_plugin_list']);

        // Adding widgets
        add_action('widgets_init', [$this, 'wpr_load_widget']);
    }

    /**
     * @return bool
     */

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

    public function wpr_load_widget()
    {
        register_widget(new WPR_Widget($this->config));
    }

}
