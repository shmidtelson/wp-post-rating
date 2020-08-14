<?php
declare(strict_types=1);

namespace WPR\Service;

class ScriptsService extends AbstractService
{
    const SCRIPT_NAME = 'wp-post-rating';
	/**
	 * Активация скриптов
	 */
    public function initScripts(): void
    {
        /**
         * Main files
         */
        wp_enqueue_style(
            self::SCRIPT_NAME,
            $this->config->getPluginCssPath() . 'wp-post-rating.min.css',
            [],
            ConfigService::PLUGIN_VERSION,
            'all'
        );

        wp_enqueue_script(
            self::SCRIPT_NAME,
            $this->config->getPluginJSPath() . 'wp-post-rating.min.js',
            ['jquery'],
            ConfigService::PLUGIN_VERSION,
            true
        );

        // TODO: Вернуть настройки
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