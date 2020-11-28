<?php

declare(strict_types=1);

namespace WPR\Abstractions\Traits;

use WPR\Service\ConfigService;
use WPR\Exception\BaseException;

trait GetConfigServiceTrait
{
    private function getConfig(): ConfigService
    {
        if (! empty($this->container)) {
            return $this->container->get(ConfigService::class);
        }

        throw new BaseException('I need container... Please, add container');
    }
}
