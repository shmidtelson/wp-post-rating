<?php

declare(strict_types=1);

namespace WPR\Repository;

class SettingRepository
{
    const SETTINGS_KEY = 'wpr_settings';

    const SETTINGS_GROUP_KEY = 'wpr_options_group';

    public function registerSettingsGroup(): void
    {
        register_setting(self::SETTINGS_GROUP_KEY, self::SETTINGS_KEY);
    }

    public function set(string $jsonString): void
    {
        update_option(self::SETTINGS_KEY, $jsonString);
    }

    /**
     * @return string|false
     */
    public function get()
    {
        return get_option(self::SETTINGS_KEY);
    }
}
