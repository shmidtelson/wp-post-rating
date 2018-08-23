<?php

class Database
{


    public function __construct()
    {

    }

    private function plugin_install()
    {
        global $wpdb;

        $table_name = $this->get_table_name();
        if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

            $sql = $this->sql_create_table();

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);

//            $rows_affected = $wpdb->insert($table_name, ['time' => current_time('mysql'), 'name' => $welcome_name, 'text' => $welcome_text)];

            add_option("wpr_rating_db_version", self::$PLUGIN_TABLE_NAME);

        }
    }

    private function get_table_name()
    {
        global $wpdb;
        return $wpdb->prefix . self::$PLUGIN_TABLE_NAME;
    }

    private function sql_create_table()
    {
        return "CREATE TABLE " . $this->get_table_name() . " (
	  id mediumint(9) NOT NULL AUTO_INCREMENT,
	  time bigint(11) DEFAULT '0' NOT NULL,
	  name tinytext NOT NULL,
	  text text NOT NULL,
	  url VARCHAR(55) NOT NULL,
	  UNIQUE KEY id (id)
);";
    }
}