<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Abstractions\Abstracts\AbstractService;
use WPR\Entity\SettingEntity;
use WPR\Repository\SettingRepository;

class SettingService extends AbstractService
{
    public function setDefaultSettings(): void
    {
        $settingsEntity = new SettingEntity();
        $this->getRepository()->setDefaultSettings(json_encode($settingsEntity));
    }

    public function getSetting(): SettingEntity
    {
        $settingsEntity = new SettingEntity();
        $data = json_decode($this->getRepository()->get(), true);

        if ($data === null) {
            return $settingsEntity;
        }

        $settingsEntity->loadData($data);

        return $settingsEntity;
    }

    /**
     * @param SettingEntity $settingDto
     */
    public function save(SettingEntity $settingDto): void
    {
        $this->getRepository()->set(json_encode($settingDto));
    }

    private function getRepository(): SettingRepository
    {
        return $this->container->get(SettingRepository::class);
    }
}
