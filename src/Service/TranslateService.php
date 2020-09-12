<?php

declare(strict_types=1);

namespace WPR\Service;

class TranslateService
{
    private $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function loadPluginTextDomain()
    {
        $locale = apply_filters('plugin_locale', get_locale(), ConfigService::PLUGIN_NAME);
        if ($loaded = load_textdomain(
            ConfigService::PLUGIN_NAME,
            $this->configService->getPluginPath().'languages'.DIRECTORY_SEPARATOR.ConfigService::PLUGIN_NAME.'-'.$locale.'.mo'
        )) {
            return $loaded;
        }

        return load_plugin_textdomain(ConfigService::PLUGIN_NAME, false, $this->configService->getPluginPath().'/languages/');
    }
}
