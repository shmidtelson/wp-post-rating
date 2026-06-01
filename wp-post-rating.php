<?php
/*
Plugin Name: Wp Post Rating
Plugin URI: https://github.com/shmidtelson/wp-post-rating
Description: Powerful post rating wordpress plugin
Version: 1.2.5
Requires at least: 6.0
Requires PHP: 8.1
Author: Romua1d
Author URI: https://romua1d.ru
Text Domain: wp-post-rating
Domain Path: /languages
License: MIT
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

if (!defined('WPR_DEBUG')) {
    /*
     * Enable plugin debug mod.
     */
    define('WPR_DEBUG', false);
}
/**
 * @return WPR_Vendor\Symfony\Component\DependencyInjection\ContainerBuilder
 */
function wpr_build_container()
{
    $pluginNamePath = plugin_dir_path(__FILE__);
    require_once $pluginNamePath.'vendor/autoload.php';
    WPR\Compat\ListTableLoader::loadDependencies();

    $containerBuilder = new WPR_Vendor\Symfony\Component\DependencyInjection\ContainerBuilder();

    $loader = new WPR_Vendor\Symfony\Component\DependencyInjection\Loader\PhpFileLoader(
        $containerBuilder,
        new WPR_Vendor\Symfony\Component\Config\FileLocator(__DIR__)
    );
    $loader->load($pluginNamePath.'dependencies/services.php');

    $containerBuilder->setParameter('wpr.path', $pluginNamePath);
    $containerBuilder->setParameter('wpr.url', plugin_dir_url(__FILE__));
    $containerBuilder->setParameter('wpr.plugin_file_path', __FILE__);
    $containerBuilder->setParameter('wpr.base_name', plugin_basename(__FILE__));
    $containerBuilder->setParameter('wpr.version', '1.2.5');

    $containerBuilder->compile();

    return $containerBuilder;
}

function wpr_activate_plugin(): void
{
    $container = wpr_build_container();
    $container->get(WPR\Service\MaintenanceService::class)->installPlugin();
}

register_activation_hook(__FILE__, 'wpr_activate_plugin');

/**
 * Run plugin function.
 *
 * @throws Exception If something went wrong.
 */
function run_wp_post_rating()
{
    $containerBuilder = wpr_build_container();

    // Create DB tables if missing (e.g. after copy-wp without re-activation).
    $containerBuilder->get(WPR\Service\MaintenanceService::class)->installPlugin();

    $wpPostRating = new WPR\Plugin($containerBuilder);
    $wpPostRating->run();

    do_action('wp_post_rating_init', $wpPostRating);
}

add_action('plugins_loaded', 'run_wp_post_rating');
