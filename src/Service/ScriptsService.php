<?php

declare(strict_types=1);

namespace WPR\Service;

class ScriptsService
{
    const SCRIPT_NAME = 'wp-post-rating';

    public function __construct(
        private readonly ConfigService $configService,
        private readonly SettingService $settingService,
        private readonly PluginContext $context,
    ) {
    }

    public function initScripts(): void
    {
        wp_enqueue_style(
            self::SCRIPT_NAME,
            $this->configService->getPluginCssPath() . 'main.css',
            [],
            $this->context->version,
            'all'
        );

        wp_enqueue_script(
            self::SCRIPT_NAME,
            $this->configService->getPluginJSPath() . 'main.bundle.js',
            ['jquery'],
            $this->context->version,
            true
        );

        $this->addCssVariables('wp-post-rating');
    }

    /**
     * @param string $hook
     */
    public function initAdminScripts($hook): void
    {
        if ($hook === 'settings_page_' . ConfigService::OPTIONS_KEY) {
            wp_enqueue_script('wp-color-picker');
            wp_enqueue_style('wp-color-picker');

            wp_register_script(
                'admin-settings-page',
                $this->configService->getPluginJSPath() . 'admin.bundle.js',
                ['jquery', 'wp-color-picker']
            );

            wp_enqueue_script('admin-settings-page');
        }

        if ($hook === 'settings_page_' . ConfigService::OPTIONS_KEY || $hook === 'plugins.php') {
            wp_register_style(
                'admin-settings-page',
                $this->configService->getPluginCssPath() . 'admin.css'
            );
            wp_enqueue_style('admin-settings-page');
            $this->addCssVariables('admin-settings-page');
        }
    }

    private function addCssVariables(string $handle = 'wp-post-rating'): void
    {
        $settingsDto = $this->settingService->getSetting();
        $custom_css = sprintf(
            ':root {
	--wpr-main-color: %s;
	--wpr-text-color: %s;
	--wpr-text-background-color: %s;
}',
            $settingsDto->getStarsMainColor(),
            $settingsDto->getStarsTextColor(),
            $settingsDto->getStarsTextBackgroundColor()
        );

        wp_add_inline_style($handle, $custom_css);
    }
}
