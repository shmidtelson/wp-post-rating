<?php

declare(strict_types=1);

namespace WPR\Service;

class TranslateService extends AbstractService
{
    public function loadPluginTextDomain()
    {
        $locale = apply_filters('plugin_locale', get_locale(), ConfigService::PLUGIN_NAME);
        if ($loaded = load_textdomain(
            ConfigService::PLUGIN_NAME,
            trailingslashit(WP_LANG_DIR).ConfigService::PLUGIN_NAME.DIRECTORY_SEPARATOR.ConfigService::PLUGIN_NAME.'-'.$locale.'.mo'
        )) {
            return $loaded;
        }

        return load_plugin_textdomain(ConfigService::PLUGIN_NAME, false, $this->config->getPluginPath().'/languages/');
    }
}
