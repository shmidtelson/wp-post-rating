<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Entity\SettingEntity;
use WPR\Repository\SettingRepository;

class SettingService extends AbstractService
{
    private $repository;

    public function __construct(SettingRepository $repository)
    {
        parent::__construct();

        $this->repository = $repository;
    }

    public function setDefaultSettings(): void
    {
        $settingsEntity = new SettingEntity();
        $this->repository->setDefaultSettings(json_encode($settingsEntity));
    }

    /**
     * @return SettingEntity
     */
    public function getSetting()
    {
        $settingsEntity = new SettingEntity();
        $data = json_decode($this->repository->get(), true);

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
        $this->repository->set(json_encode($settingDto));
    }
}
