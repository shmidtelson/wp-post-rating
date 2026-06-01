<?php

declare(strict_types=1);

namespace WPR\Service;

class TranslateService
{
    private ConfigService $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function loadPluginTextDomain(): bool
    {
        $domain = ConfigService::PLUGIN_NAME;
        $locale = apply_filters('plugin_locale', determine_locale(), $domain);
        $languagesPath = $this->configService->getPluginPath().'languages';

        unload_textdomain($domain);

        $localeFile = $languagesPath.DIRECTORY_SEPARATOR.$domain.'-'.$locale.'.mo';
        if (is_readable($localeFile) && load_textdomain($domain, $localeFile)) {
            return true;
        }

        if ($locale !== 'en_US') {
            $englishFile = $languagesPath.DIRECTORY_SEPARATOR.$domain.'-en_US.mo';
            if (is_readable($englishFile)) {
                load_textdomain($domain, $englishFile);
            }
        }

        return load_plugin_textdomain(
            $domain,
            false,
            ConfigService::PLUGIN_NAME.'/languages'
        );
    }
}
