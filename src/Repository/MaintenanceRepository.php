<?php
declare(strict_types=1);

namespace WPR\Repository;

use WPR\Service\ConfigService;

class MaintenanceRepository
{
    /**
     * @var ConfigService
     */
    private $configService;

    public function __construct(ConfigService $configService)
    {
        $this->configService = $configService;
    }

    /**
     * @return bool
     */
    public function hasTable()
    {
        return $this->configService->wpdb->get_var(sprintf("show tables like '%s'", $this->configService->getTableName())) === $this->configService->getTableName();
    }

    /**
     * @return bool
     */
    public function createTable()
    {
        $sql = sprintf(
            "
CREATE TABLE %s (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	created_at DATETIME DEFAULT '1970-01-01 00:00:01',
	post_id int(8) NOT NULL,
	user_id int(8) NULL,
	vote int(1) NOT NULL,
	ip varchar(15) NULL,
	UNIQUE KEY id (id)
) %s;",
            $this->configService->getTableName(),
            $this->configService->getDatabaseCharsetCollate()
        );

        require_once(ABSPATH . 'wp-admin' . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'upgrade.php');
        dbDelta($sql);

        return true;
    }
}
