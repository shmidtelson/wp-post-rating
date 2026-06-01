<?php

declare(strict_types=1);

namespace WPR\Views\Admin;

use WPR\Compat\ListTableLoader;
use WP_List_Table;
use WPR\Service\ConfigService;
use WPR\Service\RatingService;
use WPR\Template\TemplateRenderer;

class RatingTableView extends WP_List_Table
{
    const PER_PAGE = 10;

    private bool $listTableInitialized = false;

    public function __construct(
        private readonly RatingService $serviceRating,
        private readonly TemplateRenderer $templates,
    ) {
    }

    public function loadRatingTable(): void
    {
        $this->ensureListTableInitialized();
        $this->prepare_items();

        echo $this->templates->render('admin/ratings-table', [
            'content' => $this->displayTable(),
        ]);
    }

    /**
     * @param array<string, mixed> $item
     */
    public function column_cb($item): string
    {
        return $this->templates->render('admin/fields/checkbox-column', [
            'id' => $item['id'],
        ]);
    }

    public function get_columns(): array
    {
        return [
            'cb' => $this->templates->render('admin/fields/checkbox'),
            'id' => __('id', ConfigService::PLUGIN_NAME),
            'display_name' => __('User', ConfigService::PLUGIN_NAME),
            'post_title' => __('Post', ConfigService::PLUGIN_NAME),
            'vote' => __('Vote result', ConfigService::PLUGIN_NAME),
            'ip' => __('IP adress', ConfigService::PLUGIN_NAME),
            'created_at' => __('Date create', ConfigService::PLUGIN_NAME),
        ];
    }

    public function get_sortable_columns(): array
    {
        return [
            'id' => ['id', true],
            'created_at' => ['created_at', false],
            'post_title' => ['post_title', false],
        ];
    }

    public function get_bulk_actions(): array
    {
        return [
            'delete' => __('Delete', ConfigService::PLUGIN_NAME),
        ];
    }

    public function process_bulk_action(): void
    {
        if (isset($_POST['_wpnonce']) && ! empty($_POST['_wpnonce'])) {
            $nonce = sanitize_text_field(wp_unslash($_POST['_wpnonce']));
            $action = 'bulk-' . $this->_args['plural'];

            if (! wp_verify_nonce($nonce, $action)) {
                wp_die('Nope! Security check failed!');
            }
        }

        $action = $this->current_action();

        switch ($action) {
            case 'delete':
                $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : [];

                if (! empty($ids)) {
                    $this->success_deleted($this->serviceRating->delete($ids));
                }
                break;
            case 'edit':
                wp_die('This is the edit page.');
            default:
                return;
        }
    }

    public function success_deleted($d): void
    {
        echo $this->templates->render(
            'admin/messages/success',
            ['content' => sprintf(_n('Deleted %s vote', 'Deleted %s votes', $d, ConfigService::PLUGIN_NAME), $d)]
        );
    }

    public function no_items(): void
    {
        _e('No ratings avaliable.', ConfigService::PLUGIN_NAME);
    }

    public function prepare_items(): void
    {
        $paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
        $orderBy = (isset($_REQUEST['orderby']) && in_array(
            $_REQUEST['orderby'],
            array_keys($this->get_sortable_columns()),
            true
        )) ? $_REQUEST['orderby'] : 'id';
        $order = (isset($_REQUEST['order']) && in_array(
            $_REQUEST['order'],
            ['asc', 'desc'],
            true
        )) ? $_REQUEST['order'] : 'desc';
        $offset = self::PER_PAGE * $paged;

        $this->_column_headers = [
            $this->get_columns(),
            [],
            $this->get_sortable_columns(),
        ];
        $this->process_bulk_action();

        $this->items = $this->formatRatingList($this->serviceRating->getRatingList(
            $order,
            $orderBy,
            $offset,
            self::PER_PAGE
        ));

        $totalVotes = $this->serviceRating->getTotalVotes();
        $this->set_pagination_args([
            'total_items' => $totalVotes,
            'per_page' => self::PER_PAGE,
            'total_pages' => (int) ceil($totalVotes / self::PER_PAGE),
        ]);
    }

    /**
     * @param array<string, mixed> $item
     */
    public function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * @param array<int, array<string, mixed>> $list
     * @return array<int, array<string, mixed>>
     */
    private function formatRatingList(array $list): array
    {
        return array_map(
            static function ($item) {
                $item['display_name'] = (is_null($item['display_name']))
                    ? __('Guest', ConfigService::PLUGIN_NAME)
                    : $item['display_name'];

                return $item;
            },
            $list
        );
    }

    private function displayTable(): string
    {
        ob_start();
        $this->display();
        $html = ob_get_contents();
        ob_end_clean();

        return (string) $html;
    }

    private function ensureListTableInitialized(): void
    {
        if ($this->listTableInitialized) {
            return;
        }

        ListTableLoader::loadDependencies();

        parent::__construct([
            'singular' => 'wp_list_vote',
            'plural' => 'wp_list_votes',
            'ajax' => false,
            'screen' => 'wp_list_votes',
        ]);

        $this->listTableInitialized = true;
    }
}
