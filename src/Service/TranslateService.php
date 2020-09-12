<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Abstractions\Abstracts\AbstractService;
use WPR\Abstractions\Traits\GetConfigServiceTrait;

class TranslateService extends AbstractService
{
    use GetConfigServiceTrait;

    public function loadPluginTextDomain()
    {
        $locale = apply_filters('plugin_locale', get_locale(), ConfigService::PLUGIN_NAME);
        if ($loaded = load_textdomain(
            ConfigService::PLUGIN_NAME,
            $this->getConfig()->getPluginPath().'languages'.DIRECTORY_SEPARATOR.ConfigService::PLUGIN_NAME.'-'.$locale.'.mo'
        )) {
            return $loaded;
        }

        return load_plugin_textdomain(ConfigService::PLUGIN_NAME, false, $this->getConfig()->getPluginPath().'/languages/');
    }
}
