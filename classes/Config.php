<?php

namespace WPR_Plugin;

class Config
{
    static $PLUGIN_VERSION = '1.0';
    static $PLUGIN_PATH = '';
    static $PLUGIN_URL = '';
    static $PLUGIN_NAME = 'wp-post-rating';
    static $PLUGIN_DB_VERSION = "1.0";
    static $PLUGIN_TABLE_NAME = "wpr-rating";

    public function __construct()
    {
        self::$PLUGIN_PATH = plugin_dir_path(__FILE__);
        self::$PLUGIN_URL = plugin_dir_url(__FILE__);
    }
}