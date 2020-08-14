<?php

namespace WPR_Plugin;

class Database
{
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
}