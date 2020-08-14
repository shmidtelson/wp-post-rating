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

use \DI\Container;
use \DI\ContainerBuilder;
use \WPR\Service\ConfigService;
use \WPR\Service\ScriptsService;
use \WPR\Service\DocumentService;
use \WPR\Service\TranslateService;
use \WPR\Service\MaintenanceService;
use \WPR\Service\WidgetService;
use \WPR\Views\RatingView;
use \WPR\Views\Admin\MenuItemView;
use \WPR\Wordpress\WPR_Widget;
use function \DI\create;

/**
 * Container create
 */
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions([
    ConfigService::class => create( ConfigService::class),
    ScriptsService::class => create(ScriptsService::class),
    DocumentService::class => create(DocumentService::class),
    TranslateService::class => create(TranslateService::class),
    MaintenanceService::class => create(MaintenanceService::class),

    RatingView::class => create(TranslateService::class),
    MenuItemView::class => create(MenuItemView::class),

    WPR_Widget::class =>  create(WPR_Widget::class),
]);
$containerBuilder->build();

$container = new Container();
/**
 * Wordpress hooks
 */
// Include js and css
add_action('wp_enqueue_scripts', [$container->get(ScriptsService::class), 'initScripts']);
// Add nonce to head
add_action('wp_head', [$container->get(DocumentService::class), 'addNonceToHead']);
// Load translates
add_action('init', [$container->get(TranslateService::class), 'loadPluginTextDomain']);
// Start install tables if not exists
register_activation_hook(__FILE__, [$container->get(MaintenanceService::class), 'installPlugin']);
// Add shortcodes
add_shortcode('wp_rating', [$container->get(RatingView::class), 'renderStars']);
// Add settings link
add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$container->get(MenuItemView::class), 'addSettingsLinkToPluginList']);
// Adding widgets
add_action('widgets_init', [$container->get(WidgetService::class), 'loadWidget']);
