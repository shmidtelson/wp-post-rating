<?php

declare(strict_types=1);

namespace WPR\Service;

use Throwable;
use WPR\Entity\RatingEntity;
use WPR\Repository\RatingRepository;

class RatingService
{
    /**
     * @var RatingRepository
     */
    private $repository;

    /**
     * @var ConfigService
     */
    private $configService;

    public function __construct(RatingRepository $repository, ConfigService $configService)
    {
        $this->repository = $repository;
        $this->configService = $configService;
    }

    /**
     * @param int $postId
     *
     * @return array|object|null
     */
    public function getUserLatestVoteByPostId(int $postId)
    {
        $userId = get_current_user_id();
        if ($userId) {
            return $this->repository->getLatestVoteByPostIdAndUserId($postId, $userId);
        }

        return $this->repository->getLatestVoteByPostIdAndUserIp(
            $postId,
            $this->configService->getUserIp()
        );
    }

    /**
     * @param int  $vote
     * @param int  $postId
     * @param null $id
     *
     * @return bool
     */
    public function save(int $vote, int $postId, $id = null): bool
    {
        $entity = new RatingEntity();

        try {
            $entity->setId($id);
            $entity->setPostId($postId);
            $entity->setUserId(get_current_user_id());
            $entity->setIp($this->configService->getUserIp());
            $entity->setCreatedAt(current_time('Y-m-d H:i:s'));
            $entity->setVote($vote);

            if ($id === null) {
                $this->repository->save($entity);
            }

            if ($id > 0) {
                $this->repository->update($entity);
            }
        } catch (Throwable $e) {
            if (defined('WP_DEBUG') && WP_DEBUG) {
                error_log('WP Post Rating save error: '.$e->getMessage());
            }

            return false;
        }

        return true;
    }

    public function delete(array $ids)
    {
        $sanitizedIds = array_map('absint', $ids);
        $sanitizedIds = array_filter($sanitizedIds);

        if ($sanitizedIds === []) {
            return 0;
        }

        return $this->repository->delete(
            implode(',', $sanitizedIds)
        );
    }

    /**
     * @param int $postId
     * @param int $default
     *
     * @return int
     */
    public function getTotalVotesByPostId(int $postId, int $default = 0): int
    {
        $result = $this->repository->getTotalVotesByPostId($postId);

        if ($result[0]->total_rating === null) {
            return $default;
        }

        if ($result[0]->total_rating === '0') {
            return $default;
        }

        return (int) $result[0]->total_rating;
    }

    /**
     * @param int $postId
     * @param int $symbolsAfterDot
     * @param int $default
     *
     * @return float|int|mixed
     */
    public function getAvgRating(int $postId, int $symbolsAfterDot = 0, int $default = 0)
    {
        $result = $this->repository->getAvgRating($postId);

        if ($result[0]->avg_rating === null) {
            return $default;
        }

        return round((float) $result[0]->avg_rating, $symbolsAfterDot);
    }

    /**
     * @return string|null
     */
    public function getTotalVotes()
    {
        return $this->repository->getTotalVotes();
    }

    /**
     * @param string $order
     * @param string $orderBy
     * @param int    $offset
     * @param int    $perPage
     *
     * @return array|object|null
     */
    public function getRatingList(string $order, string $orderBy, int $offset, int $perPage)
    {
        return $this->repository->getRatingList(
            $order,
            $orderBy,
            $perPage,
            $offset
        );
    }
}
