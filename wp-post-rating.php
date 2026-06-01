<?php
/*
Plugin Name: Wp Post Rating
Plugin URI: https://github.com/shmidtelson/wp-post-rating
Description: Powerful post rating wordpress plugin
Version: 1.3.1
Requires at least: 6.0
Requires PHP: 8.1
Author: Romua1d
Author URI: https://romua1d.ru
Text Domain: wp-post-rating
Domain Path: /languages
License: MIT
*/

// Exit if accessed directly.
if (! defined('ABSPATH')) {
    exit;
}

if (! defined('WPR_DEBUG')) {
    define('WPR_DEBUG', false);
}

if (! defined('WPR_VERSION')) {
    define('WPR_VERSION', '1.3.1');
}

require_once __DIR__ . '/includes/autoload.php';

use WPR\Bootstrap\PluginBootstrap;
use WPR\Service\MaintenanceService;

/**
 * @return PluginBootstrap
 */
function wpr_bootstrap(): PluginBootstrap
{
    static $bootstrap = null;

    if ($bootstrap === null) {
        $bootstrap = PluginBootstrap::init(__FILE__, WPR_VERSION);
    }

    return $bootstrap;
}

function wpr_activate_plugin(): void
{
    wpr_bootstrap()->get(MaintenanceService::class)->installPlugin();
}

register_activation_hook(__FILE__, 'wpr_activate_plugin');

/**
 * Run plugin.
 */
function run_wp_post_rating(): void
{
    $bootstrap = wpr_bootstrap();

    // Create DB tables if missing (e.g. after copy-wp without re-activation).
    $bootstrap->get(MaintenanceService::class)->installPlugin();

    $plugin = $bootstrap->plugin();
    $plugin->run();

    do_action('wp_post_rating_init', $plugin);
}

add_action('plugins_loaded', 'run_wp_post_rating');
