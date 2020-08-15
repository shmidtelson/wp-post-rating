<?php

declare(strict_types=1);

namespace WPR\Repository;

use WPR\Entity\RatingEntity;

class RatingRepository extends AbstractRepository
{
    /**
     * @param int $postId
     * @param int $userId
     *
     * @return array|object|null
     */
    public function getLatestVoteByPostIdAndUserId(int $postId, int $userId)
    {
        $sql = sprintf(
            "
SELECT id, created_at
FROM %s
WHERE user_id = '%s' AND post_id = '%s'
ORDER BY id DESC 
LIMIT 1
",
            $this->config->getTableName(),
            $userId,
            $postId
        );

        return $this->wpdb->get_results($sql);
    }

    /**
     * @param int $postId
     * @param int $userIp
     *
     * @return array|object|null
     */
    public function getLatestVoteByPostIdAndUserIp(int $postId, int $userIp)
    {
        $sql = sprintf(
            "
SELECT id, created_at
FROM %s
WHERE ip = '%s' AND post_id = '%s'
ORDER BY id DESC 
LIMIT 1
",
            $this->config->getTableName(),
            $userIp,
            $postId
        );

        return $this->wpdb->get_results($sql);
    }

    /**
     * @param RatingEntity $entity
     *
     * @return bool|int
     */
    public function save(RatingEntity $entity)
    {
        return $this->wpdb->insert($this->config->getTableName(), (array)$entity);
    }

    /**
     * @param RatingEntity $entity
     *
     * @return bool|int
     */
    public function update(RatingEntity $entity)
    {
        return $this->wpdb->update(
            $this->config->getTableName(),
            (array)$entity,
            ['id' => $entity->getId()]
        );
    }

    /**
     * @param $postId
     *
     * @return mixed
     */
    public function getTotalVotesByPostId(int $postId)
    {
        $sql = sprintf(
            '
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
        $sql = sprintf(
            '
SELECT avg(vote) as avg_rating 
FROM %s
WHERE post_id = %s',
            $this->config->getTableName(),
            $postId
        );

        return $this->wpdb->get_results($sql);
    }
}
