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

if (version_compare(phpversion(), '7.2.5', '<')) {
    function plugin_name_php_notice()
    {
        ?>
        <div class="notice notice-error">
            <p>
                <?php
                echo wp_kses(
                    __('The minimum version of PHP is <strong>7.2.5</strong>. Please update the PHP on your server and try again.', 'plugin_name'),
                    [
                        'strong' => [],
                    ]
                ); ?>
            </p>
        </div>

        <?php
        // In case this is on plugin activation.
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
    }

    add_action('admin_notices', 'plugin_name_php_notice');

    // Don't process the plugin code further.
    return;
}

if (!defined('PLUGIN_NAME_DEBUG')) {
    /*
     * Enable plugin debug mod.
     */
    define('PLUGIN_NAME_DEBUG', false);
}
/*
 * Path to the plugin root directory.
 */
define('PLUGIN_NAME_PATH', plugin_dir_path(__FILE__));
/*
 * Url to the plugin root directory.
 */
define('PLUGIN_NAME_URL', plugin_dir_url(__FILE__));

/**
 * Run plugin function.
 *
 * @throws Exception If something went wrong.
 */
function run_wp_post_rating()
{
    require_once PLUGIN_NAME_PATH.'vendor/autoload.php';

    $containerBuilder = new ContainerBuilder();
    $loader = new PhpFileLoader($containerBuilder, new FileLocator(__DIR__));
    $loader->load(PLUGIN_NAME_PATH.'dependencies/services.php');
    $containerBuilder->compile();

    $wpPostRating = new Plugin($containerBuilder);
    $wpPostRating->run();

    do_action('wp_post_rating_init', $wpPostRating);
}

add_action('plugins_loaded', 'run_wp_post_rating');
