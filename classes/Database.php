<?php

namespace WPR_Plugin;

class Database
{
    public function __construct($config)
    {
        // load config
        $this->config = $config;

    }

    private function sql_create_table()
    {
        global $wpdb;

        return "CREATE TABLE {$this->config->PLUGIN_FULL_TABLE_NAME} (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        created_at DATETIME DEFAULT '1970-01-01 00:00:01',
        post_id int(8) NOT NULL,
        user_id int(8) NULL,
        vote int(1) NOT NULL,
        ip varchar(15) NULL,
        UNIQUE KEY id (id)
) {$wpdb->get_charset_collate()} ;";
    }

    public function create($data)
    {

        global $wpdb;
        $user_id = get_current_user_id();
        if ($user_id)
            $data['user_id'] = $user_id;
        $data['ip'] = $_SERVER['HTTP_CLIENT_IP'] ? $_SERVER['HTTP_CLIENT_IP'] : ($_SERVER['HTTP_X_FORWARDE‌​D_FOR'] ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
        $data['created_at'] = current_time("Y-m-d H:i:s");
        $return = $wpdb->insert($this->config->PLUGIN_FULL_TABLE_NAME, $data);

        return $return;
    }

    public function read($post_id)
    {
        global $wpdb;
        $user_id = get_current_user_id();

        $filter = ($user_id) ?
            "WHERE user_id = '{$user_id}' and post_id = '{$post_id}'" :
            "WHERE ip = '{$this->config->user_ip}' and post_id = '{$post_id}'";

        $result = $wpdb->get_results("
        SELECT id, created_at
        FROM {$this->config->PLUGIN_FULL_TABLE_NAME} 
        {$filter}
        ORDER BY id DESC 
        LIMIT 1
");
        return $result;
    }

    public function update($id, $data)
    {
        global $wpdb;

        $return = $wpdb->update(
            $this->config->PLUGIN_FULL_TABLE_NAME,
            ['vote' => $data['vote']],
            ['id' => $id]
        );

        return $return;
    }

    public function delete()
    {
    }

    /**
     * @param $post_id
     * @param $prec
     * @return bool|float
     */
    public function get_avg_rating($post_id, $prec)
    {
        global $wpdb;
        $result = $wpdb->get_results("SELECT avg(vote) as avg_rating FROM {$this->config->PLUGIN_FULL_TABLE_NAME} WHERE post_id = {$post_id}");
        if ($result[0]->avg_rating !== null) {
            $return = round((float)$result[0]->avg_rating, $prec);
        } else {
            $return = 0;
        }

        return $return;
    }

    /**
     * @param $post_id
     * @return bool
     */
    public function get_total_votes($post_id)
    {
        global $wpdb;
        $result = $wpdb->get_results("SELECT COUNT(id) as total_rating FROM {$this->config->PLUGIN_FULL_TABLE_NAME} WHERE post_id = {$post_id}");
        if ($result[0]->total_rating !== null) {
            $return = $result[0]->total_rating;
        } else {
            $return = 0;
        }

        return $return;
    }

}