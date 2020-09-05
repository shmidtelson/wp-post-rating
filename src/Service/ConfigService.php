<?php

declare(strict_types=1);

namespace WPR\Service;

class ConfigService
{
    const PLUGIN_TABLE_NAME = 'wpr_rating';

    const PLUGIN_NONCE_KEY = 'WPR_rating_key';

    const PLUGIN_VERSION = '1.1.0.4';

    const PLUGIN_NAME = 'wp-post-rating';

    const PLUGIN_DB_VERSION = '1.1';

    const PLUGIN_VOTE_INTERVAL = '1 day';

    const USERS_TABLE_NAME = 'users';

    const POSTS_TABLE_NAME = 'posts';

    const OPTIONS_KEY = 'wpr-settings';

    public function __construct()
    {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    /**
     * @return string
     *                Return table name of plugin
     */
    public function getTableName()
    {
        return $this->wpdb->prefix.self::PLUGIN_TABLE_NAME;
    }

    /**
     * @return string
     */
    public function getUsersTableName()
    {
        return $this->wpdb->prefix.self::USERS_TABLE_NAME;
    }

    /**
     * @return string
     */
    public function getPostsTableName()
    {
        return  $this->wpdb->prefix.self::POSTS_TABLE_NAME;
    }

    /**
     * @return string
     */
    public function getDatabaseCharsetCollate()
    {
        return $this->wpdb->get_charset_collate();
    }

    /**
     * @return string
     */
    public function getPluginPath()
    {
        return plugin_dir_path(dirname(dirname(__FILE__)));
    }

    /**
     * @return string
     */
    public function getPluginUrl()
    {
        return plugin_dir_url(dirname(dirname(__FILE__)));
    }

    /**
     * @return mixed
     */
    public function getUserIp()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        }

        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }

        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * @return string
     */
    public function getPluginCssPath()
    {
        return $this->getPluginUrl().'assets/css/';
    }

    /**
     * @return string
     */
    public function getPluginJSPath()
    {
        return $this->getPluginUrl().'assets/js/min/';
    }
}
