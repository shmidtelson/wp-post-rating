<?php
/*
Plugin Name: Wp Post Rating
Plugin URI: http://romua1d.ru/wp_post_rating
Description: Powerful post rating wordpress plugin.
Version: 1.1.0.3
Author: Romua1d
Author URI: https://romua1d.ru
Text Domain: wp-post-rating
License: MIT
*/

//* Don't access this file directly
defined('ABSPATH') or die();

require_once 'vendor/autoload.php';

use DI\Container;
use function DI\create;
use DI\ContainerBuilder;
use WPR\Views\RatingView;
use WPR\Service\AjaxService;
use WPR\Wordpress\WPR_Widget;
use WPR\Service\ConfigService;
use WPR\Service\WidgetService;
use WPR\Service\ScriptsService;
use WPR\Service\SettingService;
use WPR\Service\DocumentService;
use WPR\Service\TranslateService;
use WPR\Views\Admin\MenuItemView;
use WPR\Service\MaintenanceService;
use WPR\Service\SettingFormService;
use WPR\Service\Admin\AdminMenuService;
use WPR\Service\WordpressFunctionsService;

#################################################
############## Container create #################
#################################################
$containerBuilder = new ContainerBuilder();
$containerBuilder->useAutowiring(true);
$containerBuilder->useAnnotations(false);
$containerBuilder->addDefinitions([
    AjaxService::class => create(AjaxService::class),
    ConfigService::class => create(ConfigService::class),
    ScriptsService::class => create(ScriptsService::class),
    DocumentService::class => create(DocumentService::class),
    TranslateService::class => create(TranslateService::class),
    MaintenanceService::class => create(MaintenanceService::class),
    SettingService::class => create(SettingService::class),
    SettingFormService::class => create(SettingFormService::class),
    WordpressFunctionsService::class => create(WordpressFunctionsService::class),

    AdminMenuService::class => create(AdminMenuService::class),

    RatingView::class => create(TranslateService::class),
    MenuItemView::class => create(MenuItemView::class),

    WPR_Widget::class => create(WPR_Widget::class),
]);
$containerBuilder->build();
$container = new Container();

#################################################
############## Wordpress hooks ##################
#################################################

// Include js and css
add_action('wp_enqueue_scripts', [$container->get(ScriptsService::class), 'initScripts']);
// Admin Scripts
add_action('admin_enqueue_scripts', [$container->get(ScriptsService::class), 'initAdminScripts']);
// Add nonce to head
add_action('wp_head', [$container->get(DocumentService::class), 'addNonceToHead']);
// Load translates
add_action('init', [$container->get(TranslateService::class), 'loadPluginTextDomain']);
// Start install tables if not exists
register_activation_hook(__FILE__, [$container->get(MaintenanceService::class), 'installPlugin']);
// Add shortcodes
add_shortcode('wp_rating', [$container->get(RatingView::class), 'renderStars']);
add_shortcode('wp_rating_total', [$container->get(RatingView::class), 'getRatingTotal']);
add_shortcode('wp_rating_avg', [$container->get(RatingView::class), 'getRatingAvg']);
// Add settings link
add_filter('plugin_action_links_'.plugin_basename(__FILE__), [$container->get(MenuItemView::class), 'addSettingsLinkToPluginList']);
// Add widgets
add_action('widgets_init', [$container->get(WidgetService::class), 'loadWidget']);
// Add ajax
add_action('wp_ajax_nopriv_wpr_voted', [$container->get(AjaxService::class), 'actionVote']);
add_action('wp_ajax_wpr_voted', [$container->get(AjaxService::class), 'actionVote']);
// Add settings page to admin menu
add_action('admin_menu', [$container->get(AdminMenuService::class), 'addMenuSection']);
// Settings
add_action('admin_init', [$container->get(SettingService::class), 'setDefaultSettings']);
// Settings save form
add_action('admin_post_wpr-update', [$container->get(SettingFormService::class), 'saveForm']);
add_action('admin_notices', [$container->get(SettingFormService::class), 'successMessage']);
