<?php

namespace WPR_Plugin\Admin;

use WPR_Plugin\Config;

class Admin
{
    /**
     * Admin constructor.
     */
    public function __construct()
    {
        $this->config = new Config();
        add_action('admin_menu', [$this, 'add_plugin_admin_menu']);
    }

    /**
     * Callback for the user sub-menu in define_admin_hooks() for class Init.
     *
     * @since    1.0.0
     */
    public function add_plugin_admin_menu()
    {
        add_submenu_page(
            'options-general.php',
            __('Stars rating', $this->config->PLUGIN_NAME), //page title
            __('Stars rating', $this->config->PLUGIN_NAME), //menu title
            'manage_options', //capability
            $this->config->PLUGIN_NAME, //menu_slug,
            [$this, 'load_rating_table']
        );
    }

    /**
     * Load from template
     */
    public function load_rating_table()
    {
        // query, filter, and sort the data
        $this->user_list_table = new RatingsList($this->config->PLUGIN_NAME, $this->config);
        // render the List Table
        require_once($this->config->PLUGIN_PATH . 'templates/admin_ratings_list.php');
    }
}