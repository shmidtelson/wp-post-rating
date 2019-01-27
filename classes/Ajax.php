<?php
namespace WPR_Plugin;

class Ajax
{
    public function __construct($config, $database)
    {
        $this->config = $config;
        $this->database = $database;
        add_action('wp_ajax_nopriv_wpr_voted', [$this, 'wpr_voted']);
        add_action('wp_ajax_wpr_voted', [$this, 'wpr_voted']);
    }

    public function wpr_voted()
    {
        $result = [];

        check_ajax_referer($this->config->PLUGIN_NONCE_KEY, 'nonce');

        $data = [
            'post_id' => intval($_POST['post_id']),
            'vote' => sanitize_text_field($_POST['vote']),
        ];

        $latest_voting = $this->database->read($data['post_id']);

        if (count($latest_voting)) {
            $now = new \DateTime();
            $date = new \DateTime($latest_voting[0]->created_at);
            $date_limit = $date->modify($this->config->PLUGIN_VOTE_INTERVAL);

            if ($now < $date_limit) {
                $this->database->update($latest_voting[0]->id, $data);
                $result['action'] = 'updated';
            } else {
                $this->database->create($data);
                $result['action'] = 'created';
            }
        } else {
            $this->database->create($data);
            $result['action'] = 'created';
        }
        $result['status'] = 'ok';
        $result['avg'] = $this->database->get_avg_rating($data['post_id'],0);
        $result['total'] = $this->database->get_total_votes($data['post_id']);

        echo json_encode($result);
        wp_die();
    }
}