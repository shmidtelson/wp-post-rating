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
            'singular' => 'wp_list_vote', //Singular label
            'plural' => 'wp_list_votes', //plural label, also this well be one of the table css class
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
            'display_name' => __('User', $this->config->PLUGIN_NAME),
            'post_title' => __('Post', $this->config->PLUGIN_NAME),
            'vote' => __('Vote result', $this->config->PLUGIN_NAME),
            'ip' => __('IP adress', $this->config->PLUGIN_NAME),
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
            'post_title' => ['post_title', false],
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

    function column_date_submitted($item)
    {

        $actions = array(
            'edit' => sprintf('<a href="?page=view_application&application=%s">View</a>', $item->id),
            'delete' => sprintf('<a href="?page=view_application&application=%s">Delete</a>', $item->id, $item->id)
        );

        return sprintf('%1$s %2$s', $item->date_submitted, $this->row_actions($actions));

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
        // security check!
        if (isset($_POST['_wpnonce']) && !empty($_POST['_wpnonce'])) {

            $nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
            $action = 'bulk-' . $this->_args['plural'];

            if (!wp_verify_nonce($nonce, $action))
                wp_die('Nope! Security check failed!');

        }

        $action = $this->current_action();

        switch ($action) {
            case 'delete':
                $idsa = $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : [];
                if (is_array($ids)) $ids = implode(',', $ids);
                if (!empty($ids)) {
                    $d = $wpdb->query("DELETE FROM {$this->config->PLUGIN_FULL_TABLE_NAME} WHERE id IN($ids)");
                    $this->success_deleted($d);
                }
                break;
            case
            'edit':
                wp_die('This is the edit page.');
                break;

            default:
                // do nothing or something else
                return;
                break;
        }

        return;
    }

    public function success_deleted($d)
    {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo sprintf(_n('Deleted %s vote', 'Deleted %s votes', $d, $this->config->PLUGIN_NAME), $d); ?></p>
        </div>
        <?php
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
        $users_table = $wpdb->prefix . 'users';
        $posts_table = $wpdb->prefix . 'posts';

        $formatted_items = $wpdb->get_results(
            <<<SQL
SELECT t1.id,t2.display_name,t3.post_title,t1.vote,t1.ip,t1.created_at 
FROM {$this->config->PLUGIN_FULL_TABLE_NAME} as t1
LEFT JOIN {$users_table} as t2 ON t1.user_id = t2.id
LEFT JOIN {$posts_table} as t3 ON t1.post_id = t3.ID
ORDER BY {$orderby} {$order}
LIMIT {$per_page} 
OFFSET {$paged}
SQL
            , ARRAY_A
        );

        $formatted_items = array_map(
            function ($item) {
                $item['display_name'] = (is_null($item['display_name'])) ? 'Гость' : $item['display_name'];
                return $item;
            }, $formatted_items
        );

        $this->items = $formatted_items;

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


