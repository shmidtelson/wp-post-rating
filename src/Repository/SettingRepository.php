<?php
declare(strict_types=1);

namespace WPR\Repository;



use WPR\Abstractions\Abstracts\AbstractService;

class SettingRepository extends AbstractService
{
    const SETTINGS_KEY = 'wpr_settings';
    const SETTINGS_GROUP_KEY = 'wpr_options_group';

    /**
     * @param $jsonString
     */
    public function setDefaultSettings($jsonString)
    {
        add_option(self::SETTINGS_KEY, $jsonString);
        register_setting(self::SETTINGS_GROUP_KEY, self::SETTINGS_KEY);
    }

    /**
     * @param $jsonString
     */
    public function set(string $jsonString)
    {
        update_option(self::SETTINGS_KEY, $jsonString);
    }

    /**
     * @return string
     */
    public function get()
    {
        return get_option(self::SETTINGS_KEY);
    }
}
