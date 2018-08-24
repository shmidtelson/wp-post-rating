<?php

namespace WPR_Plugin\Admin;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/screen.php');
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class RatingsList extends \WP_List_Table
{
    private $config = [];

    public function __construct($a, $config)
    {
        $this->config = $config;
        parent::__construct([
            'singular' => 'wp_list_text_link', //Singular label
            'plural' => 'wp_list_test_links', //plural label, also this well be one of the table css class
            'ajax' => false //We won't support Ajax for this table
        ]);
        $this->prepare_items();
    }

    /**
     * [REQUIRED] this is how checkbox column renders
     *
     * @param $item - row (key, value array)
     * @return HTML
     */
    function column_cb($item)
    {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%s" />',
            $item['id']
        );
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description
     *
     * @return array
     */
    public function get_columns()
    {
        $table_columns = [
            'cb' => '<input type="checkbox" />',
            'id' => __('id', $this->config->PLUGIN_NAME),
            'user' => __('User', $this->config->PLUGIN_NAME),
            'post' => __('Post', $this->config->PLUGIN_NAME),
            'vote' => __('Vote result', $this->config->PLUGIN_NAME),
            'created_at' => __('Date create', $this->config->PLUGIN_NAME),
        ];
        return $table_columns;
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort
     *
     * @return array
     */
    function get_sortable_columns()
    {
        $sortable_columns = [
            'id' => ['id', true],
            'created_at' => ['created_at', false],
            'post_id' => ['post_id', false],
        ];
        return $sortable_columns;
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any
     *
     * @return array
     */
    function get_bulk_actions()
    {
        # TODO: DOESNT WORK, NEED FIX
        $actions = [
            'delete' => __('Delete', $this->config->PLUGIN_NAME)
        ];
        return $actions;
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part
     */
    function process_bulk_action()
    {
        global $wpdb;
        if ('delete' === $this->current_action()) {
            $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : [];
            if (is_array($ids)) $ids = implode(',', $ids);
            if (!empty($ids)) {
                $wpdb->query("DELETE FROM {$this->config->PLUGIN_FULL_TABLE_NAME} WHERE id IN($ids)");
            }
        }
    }

    public function no_items()
    {
        _e('No ratings avaliable.', $this->config->PLUGIN_NAME);
    }

    public function prepare_items()
    {

        global $wpdb;
        // prepare query params, as usual current page, order by and order direction
        $per_page = 10; // constant, how much records will be shown per page
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        // here we configure table headers, defined in our methods
        $this->_column_headers = [$columns, $hidden, $sortable];
        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        // prepare query params, as usual current page, order by and order direction
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], ['asc', 'desc'])) ? $_REQUEST['order'] : 'asc';

        // [REQUIRED] define $items array
        // notice that last argument is ARRAY_A, so we will retrieve array
        $this->items = $wpdb->get_results("SELECT * FROM {$this->config->PLUGIN_FULL_TABLE_NAME} ORDER BY {$orderby} {$order} LIMIT {$per_page} OFFSET {$paged}", ARRAY_A);

        // will be used in pagination settings
        $total_items = $wpdb->get_var("SELECT COUNT(id) FROM {$this->config->PLUGIN_FULL_TABLE_NAME}");

        // [REQUIRED] configure pagination
        $this->set_pagination_args(array(
            'total_items' => $total_items, // total items defined above
            'per_page' => $per_page, // per page constant defined at top of method
            'total_pages' => ceil($total_items / $per_page) // calculate pages count
        ));
    }

    /**
     * @param object $item
     * @param string $column_name
     */
    function column_default($item, $column_name)
    {
        return $item[$column_name];
    }
}


