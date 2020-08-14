<?php
declare(strict_types=1);

namespace WPR\Service;

use WPR\Repository\MaintenanceRepository;

class MaintenanceService extends AbstractService
{
	private $repository;

	public function __construct(MaintenanceRepository $repository)
	{
		parent::__construct();
		$this->repository = $repository;
	}

	public function installPlugin()
	{
		if (!$this->repository->hasTable()) {
			$this->repository->createTable();
			update_option("wpr_rating_db_version", $this->config::PLUGIN_DB_VERSION);
		}
	}
}