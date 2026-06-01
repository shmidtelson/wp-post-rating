<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Repository\MaintenanceRepository;
use WPR_Vendor\Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class MaintenanceService
{
    public const MINIMUM_PHP_VERSION = '8.1';

    public const MINIMUM_WORDPRESS_VERSION = '6.0';

    private MaintenanceRepository $repository;

    private string $pluginBaseName;

    public function __construct(MaintenanceRepository $repository, ParameterBagInterface $params)
    {
        $this->repository = $repository;
        $this->pluginBaseName = $params->get('wpr.base_name');
    }

    /**
     * Plugin Activation hook function to check for Minimum PHP and WordPress versions.
     */
    public function installPlugin(): void
    {
        global $wp_version;

        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            $this->stopActivatePlugin();
        }

        if (version_compare($wp_version, self::MINIMUM_WORDPRESS_VERSION, '<')) {
            $this->stopActivatePlugin();
        }

        if (! $this->repository->hasTable()) {
            $this->repository->createTable();
            update_option('wpr_rating_db_version', ConfigService::PLUGIN_DB_VERSION);
        }
    }

    public function stopActivatePlugin(): void
    {
        global $wp_version;
        deactivate_plugins($this->pluginBaseName);
        wp_die(
            sprintf(
                __('<p>The <strong>WP POST RATING</strong> plugin requires versions minimum PHP >= %s <b>(Your is %s)</b> and WP >= %s <b>(Your is %s)</b></p>'),
                self::MINIMUM_PHP_VERSION,
                PHP_VERSION,
                self::MINIMUM_WORDPRESS_VERSION,
                $wp_version
            ),
            'Plugin Activation Error',
            [
                'response' => 200,
                'back_link' => true,
            ]
        );
    }
}
