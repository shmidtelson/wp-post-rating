<?php

declare(strict_types=1);

namespace WPR\Abstractions\Traits;

use WPR\Service\SettingService;
use WPR\Exception\BaseException;

trait GetSettingsServiceTrait
{
    private function getSettings(): SettingService
    {
        if (! empty($this->container)) {
            return $this->container->get(SettingService::class);
        }

        throw new BaseException('I need container... Please, add container');
    }
}
