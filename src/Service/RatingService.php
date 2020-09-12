<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Abstractions\Abstracts\AbstractService;
use WPR\Entity\RatingEntity;
use WPR\Repository\RatingRepository;

class RatingService extends AbstractService
{
    private function getRepository(): RatingRepository
    {
        return $this->container->get(RatingRepository::class);
    }

    private function getConfig(): ConfigService
    {
        return $this->container->get(ConfigService::class);
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
            return $this->getRepository()->getLatestVoteByPostIdAndUserId($postId, $userId);
        }

        return $this->getRepository()->getLatestVoteByPostIdAndUserIp(
            $postId,
            $this->getConfig()->getUserIp()
        );
    }

    /**
     * @param int $vote
     * @param int $postId
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
            $entity->setIp($this->getConfig()->getUserIp());
            $entity->setCreatedAt(current_time('Y-m-d H:i:s'));
            $entity->setVote($vote);

            if ($id === null) {
                $this->getRepository()->save($entity);
            }

            if ($id > 0) {
                $this->getRepository()->update($entity);
            }
        } catch (\Throwable $e) {
            echo $e;
            return false;
        }

        return true;
    }

    public function delete(array $ids)
    {
        return $this->getRepository()->delete(
            implode(',', $ids)
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
        $result = $this->getRepository()->getTotalVotesByPostId($postId);

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
        $result = $this->getRepository()->getAvgRating($postId);

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
        return $this->getRepository()->getTotalVotes();
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
        return $this->getRepository()->getRatingList(
            $order,
            $orderBy,
            $perPage,
            $offset
        );
    }
}
