<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Dto\SettingDto;
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
        $settingsDto = new SettingDto();
        $this->repository->setDefaultSettings(json_encode($settingsDto));
    }

    /**
     * @return SettingDto
     */
    public function getSetting()
    {
        $settingsDto = new SettingDto();
        $data = json_decode($this->repository->get(), true);

        if ($data === null) {
            return $settingsDto;
        }

        $settingsDto->loadData($data);

        return $settingsDto;
    }

    /**
     * @param SettingDto $settingDto
     */
    public function save(SettingDto $settingDto): void
    {
        $this->repository->set(json_encode($settingDto));
    }
}
