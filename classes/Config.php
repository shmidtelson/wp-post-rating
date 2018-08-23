<?php

namespace WPR_Plugin;

class Config
{
    public $PLUGIN_VERSION = '1.0';
    public $PLUGIN_PATH;
    public $PLUGIN_URL;
    public $PLUGIN_NAME = 'wp-post-rating';
    public $PLUGIN_DB_VERSION = "1.0";
    public $PLUGIN_TABLE_NAME = "wpr_rating";

    public function __construct()
    {
        $this->PLUGIN_PATH = plugin_dir_path(dirname(__FILE__));
        $this->PLUGIN_URL = plugin_dir_url(dirname(__FILE__));
    }
}