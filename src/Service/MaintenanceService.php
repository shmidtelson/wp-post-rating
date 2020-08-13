<?php
declare(strict_types=1);

namespace WPR\Service;


use WPR\Repository\MaintenanceRepository;

class MaintenanceService extends AbstractService
{
	public function __construct(MaintenanceRepository $repository)
	{
		parent::__construct();
		$this->repository = $repository;
	}

	public function installPlugin()
	{
		global $wpdb;

//        $installed_ver = get_option("wpr_rating_db_version");

		$table_name = $this->config->PLUGIN_FULL_TABLE_NAME;

		if ($wpdb->get_var("show tables like '$table_name'") != $table_name) {

			$sql = $this->sql_create_table();

			require_once(ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'upgrade.php');
			dbDelta($sql);

			update_option("wpr_rating_db_version", $this->config->PLUGIN_DB_VERSION);

		}
	}
}