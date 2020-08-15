<?php

declare(strict_types=1);

namespace WPR\Views\Admin;

use WPR\Service\ConfigService;
use WPR\Service\RatingService;
use WPR\Twig\TwigInitEnvironment;

if (! class_exists('WP_List_Table')) {
    require_once ABSPATH.'wp-admin/includes/screen.php';
    require_once ABSPATH.'wp-admin/includes/class-wp-list-table.php';
}

class RatingTableView extends \WP_List_Table
{
    const PER_PAGE = 10;

    private $twig;

    /**
     * @var RatingService
     */
    private $serviceRating;

    public function __construct(
        RatingService $serviceRating
    ) {
        parent::__construct([
            'singular' => 'wp_list_vote', //Singular label
            'plural' => 'wp_list_votes', //plural label, also this well be one of the table css class
            'ajax' => false, //We won't support Ajax for this table
        ]);

        $this->twig = TwigInitEnvironment::getTwigEnvironment();
        $this->serviceRating = $serviceRating;

        $this->prepare_items();
    }

    public function loadRatingTable()
    {
        echo $this->twig->render('admin/ratings-table.twig', [
                'content' => $this->displayTable(),
        ]);
    }

    /**
     * [REQUIRED] this is how checkbox column renders.
     *
     * @param $item - row (key, value array)
     *
     * @return string
     */
    public function column_cb($item)
    {
        return $this->twig->render('admin/fields/checkbox-column.twig', [
                'id' => $item['id'],
        ]);
    }

    /**
     * [REQUIRED] This method return columns to display in table
     * you can skip columns that you do not want to show
     * like content, or description.
     *
     * @return array
     */
    public function get_columns()
    {
        return [
            'cb' => $this->twig->render('admin/fields/checkbox.twig'),
            'id' => __('id', ConfigService::PLUGIN_NAME),
            'display_name' => __('User', ConfigService::PLUGIN_NAME),
            'post_title' => __('Post', ConfigService::PLUGIN_NAME),
            'vote' => __('Vote result', ConfigService::PLUGIN_NAME),
            'ip' => __('IP adress', ConfigService::PLUGIN_NAME),
            'created_at' => __('Date create', ConfigService::PLUGIN_NAME),
        ];
    }

    /**
     * [OPTIONAL] This method return columns that may be used to sort table
     * all strings in array - is column names
     * notice that true on name column means that its default sort.
     *
     * @return array
     */
    public function get_sortable_columns()
    {
        return [
            'id' => ['id', true],
            'created_at' => ['created_at', false],
            'post_title' => ['post_title', false],
        ];
    }

    /**
     * [OPTIONAL] Return array of bult actions if has any.
     *
     * @return array
     */
    public function get_bulk_actions()
    {
        return [
            'delete' => __('Delete', ConfigService::PLUGIN_NAME),
        ];
    }

    /**
     * [OPTIONAL] This method processes bulk actions
     * it can be outside of class
     * it can not use wp_redirect coz there is output already
     * in this example we are processing delete action
     * message about successful deletion will be shown on page in next part.
     */
    public function process_bulk_action()
    {
        global $wpdb;
        // security check!
        if (isset($_POST['_wpnonce']) && ! empty($_POST['_wpnonce'])) {
            $nonce = filter_input(INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING);
            $action = 'bulk-'.$this->_args['plural'];

            if (! wp_verify_nonce($nonce, $action)) {
                wp_die('Nope! Security check failed!');
            }
        }

        $action = $this->current_action();

        switch ($action) {
            case 'delete':
                $ids = $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : [];

                if (!empty($ids)) {
                    $this->success_deleted($this->serviceRating->delete($ids));
                }
                break;
            case 'edit':
                wp_die('This is the edit page.');
                // no break
            default:
                // do nothing or something else
                return;
        }
    }

    public function success_deleted($d)
    {
        echo $this->twig->render(
            'admin/messages/deleted.twig',
            ['content' => sprintf(_n('Deleted %s vote', 'Deleted %s votes', $d, ConfigService::PLUGIN_NAME), $d)]
        );
    }

    public function no_items()
    {
        _e('No ratings avaliable.', ConfigService::PLUGIN_NAME);
    }

    public function prepare_items()
    {
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderBy = (isset($_REQUEST['orderby']) && in_array(
            $_REQUEST['orderby'],
            array_keys($this->get_sortable_columns())
        )) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array(
            $_REQUEST['order'],
            ['asc', 'desc']
        )) ? $_REQUEST['order'] : 'desc';
        $offset = self::PER_PAGE * $paged;

        // here we configure table headers, defined in our methods
        $this->_column_headers = [
            $this->get_columns(),
            [],
            $this->get_sortable_columns(),
        ];
        // [OPTIONAL] process bulk action if any
        $this->process_bulk_action();

        $this->items = $this->formatRatingList($this->serviceRating->getRatingList(
            $order,
            $orderBy,
            $offset,
            self::PER_PAGE
        ));

        $totalVotes = $this->serviceRating->getTotalVotes();
        $this->set_pagination_args([
            'total_items' => $totalVotes, // total items defined above
            'per_page' => self::PER_PAGE, // per page constant defined at top of method
            'total_pages' => ceil($totalVotes / self::PER_PAGE), // calculate pages count
        ]);
    }

    /**
     * @param object $item
     * @param string $column_name
     *
     * @return mixed
     */
    public function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * @param array $list
     *
     * @return array
     */
    private function formatRatingList(array $list)
    {
        return array_map(
            function ($item) {
                $item['display_name'] = (is_null($item['display_name'])) ? __('Guest') : $item['display_name'];

                return $item;
            },
            $list
        );
    }

    /**
     * @return string
     */
    private function displayTable(): string
    {
        ob_start();
        $this->display();
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
