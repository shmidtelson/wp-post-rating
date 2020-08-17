<?php
declare(strict_types=1);

namespace WPR\Repository;

use WPR\Service\ConfigService;

abstract class AbstractRepository implements RepositoryInterface
{
    protected $config;
    protected $wpdb;

    public function __construct(ConfigService $config)
    {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->config = $config;
    }
}
