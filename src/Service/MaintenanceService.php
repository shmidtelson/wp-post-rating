<?php

declare(strict_types=1);

namespace WPR\Service;

use WPR\Repository\MaintenanceRepository;

class MaintenanceService
{
    public const MINIMUM_PHP_VERSION = '8.1';

    public const MINIMUM_WORDPRESS_VERSION = '6.0';

    public function __construct(
        private readonly MaintenanceRepository $repository,
        private readonly PluginContext $context,
    ) {
    }

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
        deactivate_plugins($this->context->basename);
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
