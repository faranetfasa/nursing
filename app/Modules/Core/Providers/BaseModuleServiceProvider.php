<?php

namespace App\Modules\Core\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

/**
 * Every module extends this provider. It registers the module's own routes,
 * migrations, views, translations and configuration file by convention:
 *
 *   app/Modules/{Module}/Config/{module}.php
 *   app/Modules/{Module}/Database/Migrations
 *   app/Modules/{Module}/Resources/views
 *   app/Modules/{Module}/Resources/lang
 *   app/Modules/{Module}/Routes/web.php
 *   app/Modules/{Module}/Routes/api.php
 */
abstract class BaseModuleServiceProvider extends ServiceProvider
{
    /** Module name, e.g. "HR". */
    protected string $module = '';

    /** Middleware applied to the module web routes. */
    protected array $webMiddleware = ['web'];

    /** Middleware applied to the module api routes. */
    protected array $apiMiddleware = ['api'];

    public function register(): void
    {
        $this->registerModuleConfig();
    }

    public function boot(): void
    {
        $this->registerModuleMigrations();
        $this->registerModuleViews();
        $this->registerModuleTranslations();
        $this->registerModuleRoutes();
    }

    public function moduleName(): string
    {
        return $this->module ?: class_basename(static::class);
    }

    /** Snake cased module name, acronym friendly: HR => hr, DynamicModules => dynamic_modules. */
    public function moduleKey(): string
    {
        return strtolower((string) preg_replace('/(?<=[a-z0-9])(?=[A-Z])/', '_', $this->moduleName()));
    }

    public function modulePath(string $path = ''): string
    {
        $base = app_path('Modules/'.$this->moduleName());

        return $path === '' ? $base : $base.DIRECTORY_SEPARATOR.ltrim($path, '/\\');
    }

    protected function registerModuleConfig(): void
    {
        $config = $this->modulePath('Config/'.$this->moduleKey().'.php');

        if (is_file($config)) {
            $this->mergeConfigFrom($config, $this->moduleKey());
        }
    }

    protected function registerModuleMigrations(): void
    {
        $migrations = $this->modulePath('Database/Migrations');

        if (is_dir($migrations)) {
            $this->loadMigrationsFrom($migrations);
        }
    }

    protected function registerModuleViews(): void
    {
        $views = $this->modulePath('Resources/views');

        if (is_dir($views)) {
            $this->loadViewsFrom($views, $this->moduleKey());
        }
    }

    protected function registerModuleTranslations(): void
    {
        $lang = $this->modulePath('Resources/lang');

        if (is_dir($lang)) {
            $this->loadTranslationsFrom($lang, $this->moduleKey());
        }
    }

    protected function registerModuleRoutes(): void
    {
        $web = $this->modulePath('Routes/web.php');

        if (is_file($web)) {
            Route::middleware($this->webMiddleware)->group($web);
        }

        $api = $this->modulePath('Routes/api.php');

        if (is_file($api)) {
            Route::middleware($this->apiMiddleware)
                ->prefix('api')
                ->group($api);
        }
    }
}
