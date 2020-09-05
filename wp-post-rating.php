<?php
/*
Plugin Name: Wp Post Rating
Plugin URI: https://github.com/shmidtelson/wp-post-rating
Description: Powerful post rating wordpress plugin
Version: 1.1.0.4
Author: Romua1d
Author URI: https://romua1d.ru
Text Domain: wp-post-rating
Domain Path: /languages
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
$wprContainerBuilder = new ContainerBuilder();
$wprContainerBuilder->useAutowiring(true);
$wprContainerBuilder->useAnnotations(false);
$wprContainerBuilder->addDefinitions([
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
$wprContainerBuilder->build();
$wprContainer = new Container();

#################################################
############## Wordpress hooks ##################
#################################################

// Include js and css
add_action('wp_enqueue_scripts', [$wprContainer->get(ScriptsService::class), 'initScripts']);
// Admin Scripts
add_action('admin_enqueue_scripts', [$wprContainer->get(ScriptsService::class), 'initAdminScripts']);
// Add nonce to head
add_action('wp_head', [$wprContainer->get(DocumentService::class), 'addNonceToHead']);
// Load translates
add_action('init', [$wprContainer->get(TranslateService::class), 'loadPluginTextDomain']);
// Start install tables if not exists
register_activation_hook(__FILE__, [$wprContainer->get(MaintenanceService::class), 'installPlugin']);
// Add shortcodes
add_shortcode('wp_rating', [$wprContainer->get(RatingView::class), 'renderStars']);
add_shortcode('wp_rating_total', [$wprContainer->get(RatingView::class), 'getRatingTotal']);
add_shortcode('wp_rating_avg', [$wprContainer->get(RatingView::class), 'getRatingAvg']);
// Add settings link
add_filter('plugin_action_links_'.plugin_basename(__FILE__), [$wprContainer->get(MenuItemView::class), 'addSettingsLinkToPluginList']);
// Add widgets
add_action('widgets_init', [$wprContainer->get(WidgetService::class), 'loadWidget']);
// Add ajax
add_action('wp_ajax_nopriv_wpr_voted', [$wprContainer->get(AjaxService::class), 'actionVote']);
add_action('wp_ajax_wpr_voted', [$wprContainer->get(AjaxService::class), 'actionVote']);
// Add settings page to admin menu
add_action('admin_menu', [$wprContainer->get(AdminMenuService::class), 'addMenuSection']);
// Settings
add_action('admin_init', [$wprContainer->get(SettingService::class), 'setDefaultSettings']);
// Settings save form
add_action('admin_post_wpr-update', [$wprContainer->get(SettingFormService::class), 'saveForm']);
add_action('admin_notices', [$wprContainer->get(SettingFormService::class), 'successMessage']);
