<?php
declare(strict_types=1);

namespace WPR\Repository;

use WPR\Service\ConfigService;

class WidgetRepository
{
    const MIN_POSTS_COUNT = 1;
    const MAX_POSTS_COUNT = 10;
    /**
     * @var ConfigService
     */
    private $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    public function getPostsFilter(int $count, string $orderBy, string $sort)
    {
        $sql = sprintf(
            "
SELECT post_id, user_id, created_at, vote
FROM %s
%s
%s
",
            $this->configService->getTableName(),
            $this->queryGetOrder($orderBy, $sort),
            $this->queryGetLimit($count)
        );

        return $this->configService->wpdb->get_results($sql, ARRAY_A);
    }

    /**
     * @param $limit
     *
     * @return string
     */
    private function queryGetLimit($limit)
    {
        if ($limit > 10) {
            $limit = self::MIN_POSTS_COUNT;
        }
        if ($limit < 1) {
            $limit = self::MAX_POSTS_COUNT;
        }

        return "LIMIT 0, {$limit}";
    }

    /**
     * @param string $orderBy
     * @param string $sort
     *
     * @return string
     */
    private function queryGetOrder($orderBy, $sort)
    {
        if ($orderBy == 'date') {
            $orderBy = 'created_at';
        }
        if ($sort == 'asc') {
            $sort = 'ASC';
        }
        if ($sort == 'desc') {
            $sort = 'DESC';
        }

        return "ORDER BY {$orderBy} {$sort}";
    }
}
