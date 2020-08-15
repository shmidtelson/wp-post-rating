<?php

declare(strict_types=1);

namespace WPR\Repository;

class RatingRepository extends AbstractRepository
{
    /**
     * @param $postId
     *
     * @return mixed
     */
    public function getTotalVotesByPostId(int $postId)
    {
        $sql = sprintf('
SELECT COUNT(id) as total_rating 
FROM %s 
WHERE post_id = %s',
            $this->config->getTableName(),
            $postId
        );

        return $this->wpdb->get_results($sql);
    }

    /**
     * @param int $postId
     *
     * @return array|object|null
     */
    public function getAvgRating(int $postId)
    {
        $sql = sprintf('
SELECT avg(vote) as avg_rating 
FROM %s
WHERE post_id = %s',
        $this->config->getTableName(),
        $postId
        );

        return $this->wpdb->get_results($sql);
    }
}
