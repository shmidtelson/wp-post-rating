<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Repository\RatingRepository;

class RatingService extends AbstractService
{
    /**
     * @var RatingRepository
     */
    private $repository;

    public function __construct(RatingRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    /**
     * @param int $postId
     * @param int $default
     *
     * @return int
     */
    public function getTotalVotesByPostId(int $postId, int $default = 1)
    {
        $result = $this->repository->getTotalVotesByPostId($postId);

        if ($result[0]->total_rating === null) {
            return $default;
        }

        if ($result[0]->total_rating === '0') {
            return $default;
        }

        return $result[0]->total_rating;
    }

    /**
     * @param int $postId
     * @param int $symbolsAfterDot
     * @param int $default
     *
     * @return float|int|mixed
     */
    public function getAvgRating(int $postId, int $symbolsAfterDot = 0, int $default = 5)
    {
        $result = $this->repository->getAvgRating($postId);

        if ($result[0]->avg_rating === null) {
            return $default;
        }

        return round((float) $result[0]->avg_rating, $symbolsAfterDot);
    }
}
