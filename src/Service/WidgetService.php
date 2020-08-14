<?php
declare(strict_types=1);

namespace WPR\Service;

use DI\Container;
use WPR\Repository\WidgetRepository;
use WPR\Wordpress\WPR_Widget;

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

    /**
     * @throws \DI\DependencyException
     * @throws \DI\NotFoundException
     */
    public function loadWidget()
    {
        register_widget((new Container())->get(WPR_Widget::class));
    }

    /**
     * @param int $count
     * @param string $orderBy
     * @param string $sort
     *
     * @return mixed
     */
    public function getPostsFilter(int $count, string $orderBy = 'date', string $sort = 'asc')
    {
        return $this->repository->getPostsFilter($count, $orderBy, $sort);
    }
}