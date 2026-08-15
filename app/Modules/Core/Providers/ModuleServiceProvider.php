<?php

namespace App\Modules\Core\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Central module loader. Reads the enabled module list from config('modules')
 * and registers each module service provider, Core always being first.
 */
class ModuleServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        foreach ($this->enabledModules() as $module) {
            $provider = $this->providerClass($module);

            if (class_exists($provider)) {
                $this->app->register($provider);
            }
        }
    }

    /**
     * @return array<int, string>
     */
    public function enabledModules(): array
    {
        $modules = config('modules.modules', []);
        $enabled = array_values(array_filter($modules, static fn ($module): bool => (bool) ($module['enabled'] ?? true)));

        return array_map(static fn ($module): string => $module['name'], $enabled);
    }

    public function providerClass(string $module): string
    {
        return sprintf('App\\Modules\\%s\\Providers\\%sServiceProvider', $module, $module);
    }
}
