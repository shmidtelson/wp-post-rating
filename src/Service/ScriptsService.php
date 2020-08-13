<?php
declare(strict_types=1);

namespace WPR\Service;

use DI\Container;

class ScriptsService extends AbstractService
{
	/**
	 * Активация скриптов
	 */
    public function initScripts(): void
    {
        /**
         * Main files
         */
        wp_enqueue_style(
            'wp-post-rating',
            $this->config->getPluginUrl() . 'assets/css/wp-post-rating.min.css',
            [],
            $this->config::PLUGIN_VERSION,
            'all'
        );

        wp_enqueue_script(
            'wp-post-rating',
            $this->config->getPluginUrl() . 'assets/js/min/wp-post-rating.min.js',
            ['jquery'],
            $this->config::PLUGIN_VERSION,
            true
        );

        $custom_css = sprintf("
:root {
	--wpr-main-color: %s;
	--wpr-second-color: %s;
	--wpr-text-color: %s;
	--wpr-text-background-color: %s;
}
",
            get_option('wpr_stars_main_color'),
            get_option('wpr_stars_second_color'),
            get_option('wpr_stars_text_color'),
            get_option('wpr_stars_text_background_color')
        );

        wp_add_inline_style('wp-post-rating', $custom_css);
    }
}