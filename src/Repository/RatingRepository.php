<?php

declare(strict_types=1);

namespace WPR\Repository;

use WPR\Entity\RatingEntity;
use WPR\Service\ConfigService;

class RatingRepository
{
    /**
     * @var ConfigService
     */
    private $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

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
            $this->configService->getTableName(),
            $userId,
            $postId
        );

        return $this->configService->wpdb->get_results($sql);
    }

    /**
     * @param int $postId
     * @param string $userIp
     *
     * @return array|object|null
     */
    public function getLatestVoteByPostIdAndUserIp(int $postId, string $userIp)
    {
        $sql = sprintf(
            "
SELECT id, created_at
FROM %s
WHERE ip = '%s' AND post_id = '%s'
ORDER BY id DESC 
LIMIT 1
",
            $this->configService->getTableName(),
            $userIp,
            $postId
        );

        return $this->configService->wpdb->get_results($sql);
    }

    /**
     * @param RatingEntity $entity
     *
     * @return bool|int
     */
    public function save(RatingEntity $entity)
    {
        return $this->configService->wpdb->insert($this->configService->getTableName(), (array)$entity);
    }

    /**
     * @param string $ids
     *
     * @return bool|int
     */
    public function delete(string $ids)
    {
        $sql = sprintf(
            '
DELETE 
FROM %s
WHERE id IN (%s)
',
            $this->configService->getTableName(),
            $ids
        );

        return $this->configService->wpdb->query($sql);
    }

    /**
     * @param RatingEntity $entity
     *
     * @return bool|int
     */
    public function update(RatingEntity $entity)
    {
        return $this->configService->wpdb->update(
            $this->configService->getTableName(),
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
            $this->configService->getTableName(),
            $postId
        );

        return $this->configService->wpdb->get_results($sql);
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
            $this->configService->getTableName(),
            $postId
        );

        return $this->configService->wpdb->get_results($sql);
    }

    /**
     * @return string|null
     */
    public function getTotalVotes()
    {
        $sql = sprintf(
            '
SELECT COUNT(id) 
FROM %s
',
            $this->configService->getTableName()
        );

        return $this->configService->wpdb->get_var($sql);
    }

    public function getRatingList(string $order, string $orderBy, int $perPage, int $offset)
    {
        $sql = sprintf(
            '
SELECT t1.id,t2.display_name,t3.post_title,t1.vote,t1.ip,t1.created_at 
FROM %s as t1
LEFT JOIN %s as t2 ON t1.user_id = t2.id
LEFT JOIN %s as t3 ON t1.post_id = t3.ID
ORDER BY %s %s
LIMIT %s
OFFSET %s
        ',
            $this->configService->getTableName(),
            $this->configService->getUsersTableName(),
            $this->configService->getPostsTableName(),
            $orderBy,
            $order,
            $perPage,
            $offset
        );

        return $this->configService->wpdb->get_results($sql, ARRAY_A);
    }
}
