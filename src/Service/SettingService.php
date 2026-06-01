<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Entity\SettingEntity;
use WPR\Repository\SettingRepository;

class SettingService
{
    /**
     * @var SettingRepository
     */
    private $repository;

    public function __construct(SettingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function setDefaultSettings(): void
    {
        $this->ensureDefaultSettings();
        $this->repository->registerSettingsGroup();
    }

    public function getSetting(): SettingEntity
    {
        $settingsEntity = new SettingEntity();
        $raw = $this->repository->get();

        if (! is_string($raw) || $raw === '') {
            $this->ensureDefaultSettings();

            return $settingsEntity;
        }

        $data = json_decode($raw, true);

        if (! is_array($data)) {
            return $settingsEntity;
        }

        $settingsEntity->loadData($data);

        return $settingsEntity;
    }

    private function ensureDefaultSettings(): void
    {
        if ($this->repository->get() !== false) {
            return;
        }

        $this->repository->set(json_encode(new SettingEntity()));
    }

    /**
     * @param SettingEntity $settingDto
     */
    public function save(SettingEntity $settingDto): void
    {
        $this->repository->set(json_encode($settingDto));
    }
}
