<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Repository\WidgetRepository;

class WidgetService
{
    /**
     * @var WidgetRepository
     */
    private $repository;

    public function __construct(WidgetRepository $repository)
    {
        $this->repository = $repository;
    }

    public function getPostsFilter(int $count, string $orderBy = 'date', string $sort = 'asc')
    {
        return $this->repository->getPostsFilter($count, $orderBy, $sort);
    }
}
