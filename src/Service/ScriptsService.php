<?php

declare(strict_types=1);

namespace WPR\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ScriptsService
{
    const SCRIPT_NAME = 'wp-post-rating';
    /**
     * @var ConfigService
     */
    private $configService;
    /**
     * @var SettingService
     */
    private $settingService;
    /**
     * @var ParameterBagInterface
     */
    private $params;

    public function __construct(
        ConfigService $configService,
        SettingService $settingService,
        ParameterBagInterface $params
    )
    {
        $this->configService = $configService;
        $this->settingService = $settingService;
        $this->params = $params;
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
            $this->configService->getPluginCssPath().'wp-post-rating.min.css',
            [],
            $this->params->get('wpr.version'),
            'all'
        );

        wp_enqueue_script(
            self::SCRIPT_NAME,
            $this->configService->getPluginJSPath().'wp-post-rating.min.js',
            ['jquery'],
            $this->params->get('wpr.version'),
            true
        );

        $this->addCssVariables('wp-post-rating');
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
                $this->configService->getPluginJSPath().'admin-settings-page.min.js',
                ['jquery', 'wp-color-picker']
            );

            wp_enqueue_script('admin-settings-page');
        }

        if ($hook === 'settings_page_'.ConfigService::OPTIONS_KEY or $hook === 'plugins.php') {
            wp_register_style(
                'admin-settings-page',
                $this->configService->getPluginCssPath().'admin-settings-page.min.css'
            );
            wp_enqueue_style('admin-settings-page');
            $this->addCssVariables('admin-settings-page');
        }
    }

    private function addCssVariables($handle = 'wp-post-rating')
    {
        $settingsDto = $this->settingService->getSetting();
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

        wp_add_inline_style($handle, $custom_css);
    }
}


