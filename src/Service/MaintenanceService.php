<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Repository\MaintenanceRepository;

class MaintenanceService extends AbstractService
{
    const MINIMUM_PHP_VERSION = '7.2';

    const MINIMUM_WORDPRESS_VERSION = '4.9.8';

    private $repository;

    private $wpVersion;

    public function __construct(MaintenanceRepository $repository)
    {
        parent::__construct();
        global $wp_version;
        $this->repository = $repository;
        $this->wpVersion = $wp_version;
    }

    /**
     * Plugin Activation hook function to check for Minimum PHP and WordPress versions.
     */
    public function installPlugin()
    {
        global $wp_version;

        if (! $this->repository->hasTable()) {
            $this->repository->createTable();
            update_option('wpr_rating_db_version', $this->config::PLUGIN_DB_VERSION);
        }

        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            $this->stopActivatePlugin();
        }

        if (version_compare($wp_version, self::MINIMUM_WORDPRESS_VERSION, '<')) {
            $this->stopActivatePlugin();
        }
    }

    public function stopActivatePlugin()
    {
        deactivate_plugins(basename(__FILE__));
        wp_die(
            sprintf(
                __('<p>The <strong>WP POST RATING</strong> plugin requires versions minimum PHP >= %s <b>(Your is %s)</b> and WP >= %s <b>(Your is %s)</b></p>'),
                self::MINIMUM_PHP_VERSION,
                PHP_VERSION,
                self::MINIMUM_WORDPRESS_VERSION,
                $this->wpVersion
            ),
            'Plugin Activation Error',
            [
                'response' => 200,
                'back_link' => true,
            ]
        );
    }
}
