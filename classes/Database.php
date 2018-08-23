<?php

namespace WPR_Plugin;

class Database
{
    public function __construct()
    {
        // load config
        $this->config = new Config();

    }

    public function plugin_install()
    {
        global $wpdb;

        $table_name = $this->config->PLUGIN_FULL_TABLE_NAME;

        if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

            $sql = $this->sql_create_table();

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

//            $rows_affected = $wpdb->insert($table_name, ['time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text)];

            add_option("wpr_rating_db_version", $this->config->PLUGIN_DB_VERSION);

        }
    }

    private function sql_create_table()
    {
        global $wpdb;

        return sprintf(
            "CREATE TABLE %s (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
	  post_id int(8) NOT NULL,
	  user_id int(8) NULL,
	  vote int(1) NOT NULL,
	  UNIQUE KEY id (id)
) %s ;", $this->config->PLUGIN_FULL_TABLE_NAME, $wpdb->get_charset_collate()
        );
    }

    private function create()
    {
    }

    private function read()
    {
    }

    private function update()
    {
    }

    private function delete()
    {
    }
}