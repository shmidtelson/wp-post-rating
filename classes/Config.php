<?php

namespace WPR_Plugin;

class Config
{
    public $PLUGIN_VERSION = '1.0';
    public $PLUGIN_DB_VERSION = '1.1';
    public $PLUGIN_PATH;
    public $PLUGIN_URL;
    public $PLUGIN_NAME = 'wp-post-rating';
    public $PLUGIN_TABLE_NAME = "wpr_rating";
    public $PLUGIN_FULL_TABLE_NAME = '';
    public $PLUGIN_NONCE_KEY = 'WPR_rating_key';
    public $PLUGIN_VOTE_INTERVAL = '1 day';
    public $user_ip = '';

    public function __construct()
    {
        global $wpdb;
        $this->PLUGIN_FULL_TABLE_NAME = $wpdb->prefix . $this->PLUGIN_TABLE_NAME;
        $this->PLUGIN_PATH = plugin_dir_path(dirname(__FILE__));
        $this->PLUGIN_URL = plugin_dir_url(dirname(__FILE__));
        $this->user_ip = $this->get_user_ip();
    }

    public function get_user_ip()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}