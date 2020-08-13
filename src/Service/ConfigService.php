<?php
declare(strict_types=1);

namespace WPR\Service;

class ConfigService
{
	const PLUGIN_TABLE_NAME = "wpr_rating";
	const PLUGIN_NONCE_KEY = "WPR_rating_key";
	const PLUGIN_VERSION = '1.0';
	const PLUGIN_NAME = 'wp-post-rating';

    public $PLUGIN_DB_VERSION = '1.1';
    public $PLUGIN_FULL_TABLE_NAME = '';
    public $PLUGIN_VOTE_INTERVAL = '1 day';

	/**
	 * @return string
	 */
    public function getTableName()
    {
	    global $wpdb;
	    return $wpdb->prefix . self::PLUGIN_TABLE_NAME;
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
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}