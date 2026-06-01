<?php

declare(strict_types=1);

namespace WPR\Service;

class ConfigService
{
    private PluginContext $context;

    const PLUGIN_TABLE_NAME = 'wpr_rating';

    const PLUGIN_NONCE_KEY = 'WPR_rating_key';

    const PLUGIN_NAME = 'wp-post-rating';

    const PLUGIN_DB_VERSION = '1.1';

    const PLUGIN_VOTE_INTERVAL = '1 day';

    const USERS_TABLE_NAME = 'users';

    const POSTS_TABLE_NAME = 'posts';

    const OPTIONS_KEY = 'wpr-settings';

    const POSITION_SHORTCODE = 'shortcode';

    const POSITION_BEFORE = 'before';

    const POSITION_AFTER = 'after';

    /**
     * @return string[]
     */
    public static function getAllowedPositions(): array
    {
        return [
            self::POSITION_SHORTCODE,
            self::POSITION_BEFORE,
            self::POSITION_AFTER,
        ];
    }

    /**
     * @var \wpdb
     */
    public $wpdb;

    public function __construct(PluginContext $context)
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->context = $context;
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
    public function getPluginPath(): string
    {
        return trailingslashit($this->context->path);
    }

    /**
     * @return string
     */
    public function getPluginUrl(): string
    {
        return trailingslashit($this->context->url);
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

        return isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash($_SERVER['REMOTE_ADDR'])) : '';
    }

    /**
     * @return string
     */
    public function getPluginCssPath()
    {
        return $this->getPluginUrl().'dist/';
    }

    /**
     * @return string
     */
    public function getPluginJSPath()
    {
        return $this->getPluginUrl().'dist/';
    }
}
