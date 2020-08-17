<?php

declare(strict_types=1);

namespace WPR\Service;

class ScriptsService extends AbstractService
{
    const SCRIPT_NAME = 'wp-post-rating';

    /**
     * @var SettingService
     */
    private $serviceSetting;

    public function __construct(SettingService $serviceSetting)
    {
        parent::__construct();

        $this->serviceSetting = $serviceSetting;
    }

    /**
     * Активация скриптов.
     */
    public function initScripts(): void
    {
        /*
         * Main files
         */
        wp_enqueue_style(
            self::SCRIPT_NAME,
            $this->config->getPluginCssPath().'wp-post-rating.min.css',
            [],
            ConfigService::PLUGIN_VERSION,
            'all'
        );

        wp_enqueue_script(
            self::SCRIPT_NAME,
            $this->config->getPluginJSPath().'wp-post-rating.min.js',
            ['jquery'],
            ConfigService::PLUGIN_VERSION,
            true
        );

        $settingsDto = $this->serviceSetting->getSetting();
        $custom_css = sprintf('
:root {
	--wpr-main-color: %s;
	--wpr-second-color: %s;
	--wpr-text-color: %s;
	--wpr-text-background-color: %s;
}',
            $settingsDto->getStarsMainColor(),
            $settingsDto->getStarsSecondColor(),
            $settingsDto->getStarsTextColor(),
            $settingsDto->getStarsTextBackgroundColor()
        );

        wp_add_inline_style('wp-post-rating', $custom_css);
    }

    /**
     * @param $hook
     */
    public function initAdminScripts($hook)
    {
        if ($hook === 'settings_page_'.ConfigService::OPTIONS_KEY) {
            /*
             * COLOR PICKER
             */
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_style('wp-color-picker');

            wp_register_script(
                'admin-settings-page',
                $this->config->getPluginJSPath().'admin-settings-page.min.js',
                ['jquery', 'wp-color-picker']
            );
            wp_register_style(
                'admin-settings-page',
                $this->config->getPluginCssPath().'admin-settings-page.min.css'
            );
            wp_enqueue_style('admin-settings-page');
            wp_enqueue_script('admin-settings-page');
        }
    }
}
