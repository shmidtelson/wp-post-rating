<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Abstractions\Abstracts\AbstractService;
use WPR\Wordpress\WPR_Widget;
use WPR\Repository\WidgetRepository;

class WidgetService extends AbstractService
{
    public function loadWidget()
    {
        register_widget($this->container->get(WPR_Widget::class));
    }

    public function getPostsFilter(int $count, string $orderBy = 'date', string $sort = 'asc')
    {
        return $this->getRepository()->getPostsFilter($count, $orderBy, $sort);
    }

    private function getRepository(): WidgetRepository
    {
        return $this->container->get(WidgetRepository::class);
    }
}
