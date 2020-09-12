<?php
/*
Plugin Name: Wp Post Rating
Plugin URI: https://github.com/shmidtelson/wp-post-rating
Description: Powerful post rating wordpress plugin
Version: 1.1.1.0
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

use WPR\Plugin;
use WPR\Vendor\Symfony\Component\Config\FileLocator;
use WPR\Vendor\Symfony\Component\DependencyInjection\ContainerBuilder;
use WPR\Vendor\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

if (!defined('WPR_DEBUG')) {
    /*
     * Enable plugin debug mod.
     */
    define('WPR_DEBUG', false);
}
/**
 * Run plugin function.
 *
 * @throws Exception If something went wrong.
 */
function run_wp_post_rating()
{
    $pluginNamePath = plugin_dir_path(__FILE__);

    require_once $pluginNamePath.'vendor/autoload.php';

    $containerBuilder = new ContainerBuilder();
    $loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__));
    $loader->load($pluginNamePath.'dependencies/services.php');

    $containerBuilder->setParameter('wpr.path', $pluginNamePath);
    $containerBuilder->setParameter('wpr.url', plugin_dir_url(__FILE__));
    $containerBuilder->setParameter('wpr.base_name', plugin_basename(__FILE__));
    $containerBuilder->setParameter('wpr.version', '1.1.1.0');

    $containerBuilder->compile();

    $wpPostRating = new Plugin($containerBuilder);
    $wpPostRating->run();

    do_action('wp_post_rating_init', $wpPostRating);
}

add_action('plugins_loaded', 'run_wp_post_rating');
