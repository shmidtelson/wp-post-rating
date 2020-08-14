<?php
/*
Plugin Name: Wp Post Rating
Plugin URI: http://romua1d.ru/wp_post_rating
Description: Powerful post rating wordpress plugin.
Version: 2.0.1
Author: Romua1d
Author URI: https://romua1d.ru
Text Domain: wp-post-rating
License: MIT
*/

//* Don't access this file directly
defined('ABSPATH') or die();

require_once 'vendor/autoload.php';
/**
 * Container create
 */
$containerBuilder = new \DI\ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions([
    \WPR\Service\ConfigService::class => \DI\create( \WPR\Service\ConfigService::class),
    \WPR\Service\ScriptsService::class => \DI\create(\WPR\Service\ScriptsService::class),
    \WPR\Service\DocumentService::class => \DI\create(\WPR\Service\DocumentService::class),
    \WPR\Service\TranslateService::class => \DI\create(\WPR\Service\TranslateService::class),
    \WPR\Views\RatingView::class => \DI\create(\WPR\Service\TranslateService::class),
    \WPR\Service\MaintenanceService::class => \DI\create(\WPR\Service\MaintenanceService::class)
        ->constructor(\WPR\Service\MaintenanceService::class),
]);
$containerBuilder->build();

$container = new \DI\Container();
/**
 * Wordpress hooks
 */
// Include js and css
add_action('wp_enqueue_scripts', [$container->get(\WPR\Service\ScriptsService::class), 'initScripts']);
// Add nonce to head
add_action('wp_head', [$container->get(\WPR\Service\DocumentService::class), 'addNonceToHead']);
// Load translates
add_action('init', [$container->get(\WPR\Service\TranslateService::class), 'loadPluginTextDomain']);
// Start install tables if not exists
register_activation_hook(__FILE__, [$container->get(\WPR\Service\MaintenanceService::class), 'installPlugin']);
// Add shortcodes
add_shortcode('wp_rating', [$container->get(\WPR\Views\RatingView::class), 'renderStars']);

class RatingDisplay
{
    public $position = 'shortcode';
    public $wprStarsMainColor;
    public $wprStarsSecondColor;
    public $wprStarsTextColor;
    public $wprStarsTextBackgroundColor;

    public function __construct()
    {

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

    public function wpr_load_widget()
    {
        register_widget(new WPR_Widget($this->config));
    }

}
