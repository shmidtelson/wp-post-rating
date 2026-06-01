<?php

declare(strict_types=1);

namespace WPR\Bootstrap;

use InvalidArgumentException;
use WPR\Compat\ListTableLoader;
use WPR\Plugin;
use WPR\Repository\MaintenanceRepository;
use WPR\Repository\RatingRepository;
use WPR\Repository\SettingRepository;
use WPR\Repository\WidgetRepository;
use WPR\Service\Admin\AdminMenuService;
use WPR\Service\Admin\SettingsHookService as AdminSettingsHookService;
use WPR\Service\AjaxService;
use WPR\Service\ConfigService;
use WPR\Service\MaintenanceService;
use WPR\Service\PluginContext;
use WPR\Service\PostContentHookService;
use WPR\Service\RatingService;
use WPR\Service\ScriptsService;
use WPR\Service\SettingFormService;
use WPR\Service\SettingService;
use WPR\Service\SettingsHookService;
use WPR\Service\TranslateService;
use WPR\Service\WidgetService;
use WPR\Service\WordpressFunctionsService;
use WPR\Template\TemplateRenderer;
use WPR\Views\Admin\MenuItemView;
use WPR\Views\Admin\RatingTableView;
use WPR\Views\Admin\SettingsView;
use WPR\Views\NonceView;
use WPR\Views\RatingView;
use WPR\Views\SchemaOrgView;
use WPR\Wordpress\WPR_Widget;

final class PluginBootstrap
{
    /** @var array<class-string, object> */
    private array $services = [];

    private bool $adminServicesRegistered = false;

    /** @var list<class-string> */
    private const ADMIN_SERVICES = [
        RatingTableView::class,
        SettingsView::class,
        MenuItemView::class,
        AdminMenuService::class,
        SettingFormService::class,
        AdminSettingsHookService::class,
    ];

    private function __construct(PluginContext $context)
    {
        $this->services[PluginContext::class] = $context;

        $config = new ConfigService($context);
        $this->services[ConfigService::class] = $config;

        $templates = new TemplateRenderer(rtrim($context->path, '/') . '/templates/');
        $this->services[TemplateRenderer::class] = $templates;

        $maintenanceRepository = new MaintenanceRepository($config);
        $ratingRepository = new RatingRepository($config);
        $settingRepository = new SettingRepository();
        $widgetRepository = new WidgetRepository($config);

        $this->services[MaintenanceService::class] = new MaintenanceService(
            $maintenanceRepository,
            $context
        );
        $this->services[SettingService::class] = new SettingService($settingRepository);
        $this->services[RatingService::class] = new RatingService($ratingRepository, $config);
        $this->services[WordpressFunctionsService::class] = new WordpressFunctionsService();
        $this->services[ScriptsService::class] = new ScriptsService(
            $config,
            $this->get(SettingService::class),
            $context
        );
        $this->services[TranslateService::class] = new TranslateService($config);
        $this->services[AjaxService::class] = new AjaxService($this->get(RatingService::class));

        $schemaView = new SchemaOrgView(
            $templates,
            $this->get(SettingService::class),
            $this->get(RatingService::class),
            $this->get(WordpressFunctionsService::class)
        );
        $this->services[SchemaOrgView::class] = $schemaView;

        $ratingView = new RatingView(
            $this->get(WordpressFunctionsService::class),
            $this->get(RatingService::class),
            $schemaView,
            $templates,
            $this->get(SettingService::class)
        );
        $this->services[RatingView::class] = $ratingView;

        $this->services[NonceView::class] = new NonceView($templates);
        $this->services[PostContentHookService::class] = new PostContentHookService(
            $this->get(SettingService::class),
            $ratingView
        );
        $this->services[SettingsHookService::class] = new SettingsHookService(
            $this->get(ScriptsService::class),
            $this->get(NonceView::class),
            $this->get(PostContentHookService::class)
        );

        $this->services[WidgetService::class] = new WidgetService($widgetRepository);
    }

    public static function init(string $pluginFile, string $version): self
    {
        $path = plugin_dir_path($pluginFile);

        return new self(
            new PluginContext(
                file: $pluginFile,
                path: $path,
                url: plugin_dir_url($pluginFile),
                version: $version,
                basename: plugin_basename($pluginFile),
            )
        );
    }

    /**
     * @template T of object
     * @param class-string<T> $class
     * @return T
     */
    public function get(string $class): object
    {
        if (! isset($this->services[$class]) && in_array($class, self::ADMIN_SERVICES, true)) {
            $this->registerAdminServices();
        }

        if (! isset($this->services[$class]) && $class === WPR_Widget::class) {
            $this->services[WPR_Widget::class] = new WPR_Widget($this->get(WidgetService::class));
        }

        if (! isset($this->services[$class])) {
            throw new InvalidArgumentException(sprintf('Unknown service: %s', $class));
        }

        return $this->services[$class];
    }

    private function registerAdminServices(): void
    {
        if ($this->adminServicesRegistered) {
            return;
        }

        ListTableLoader::loadDependencies();

        $context = $this->get(PluginContext::class);
        $templates = $this->get(TemplateRenderer::class);

        $ratingTableView = new RatingTableView($this->get(RatingService::class), $templates);
        $this->services[RatingTableView::class] = $ratingTableView;

        $settingsView = new SettingsView($templates, $this->get(SettingService::class));
        $this->services[SettingsView::class] = $settingsView;

        $this->services[MenuItemView::class] = new MenuItemView($templates);
        $this->services[AdminMenuService::class] = new AdminMenuService(
            $templates,
            $ratingTableView,
            $settingsView,
            $context
        );
        $this->services[SettingFormService::class] = new SettingFormService(
            $templates,
            $this->get(SettingService::class)
        );
        $this->services[AdminSettingsHookService::class] = new AdminSettingsHookService(
            $this->get(ScriptsService::class),
            $this->get(MenuItemView::class),
            $this->get(AdminMenuService::class),
            $this->get(SettingService::class),
            $this->get(SettingFormService::class),
            $context
        );

        $this->adminServicesRegistered = true;
    }

    public function plugin(): Plugin
    {
        return new Plugin($this);
    }
}
