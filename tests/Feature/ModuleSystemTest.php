<?php

namespace Tests\Feature;

use Tests\TestCase;

class ModuleSystemTest extends TestCase
{
    public function test_every_module_of_the_specification_exists_with_a_service_provider(): void
    {
        foreach (array_keys((array) config('modules.modules')) as $module) {
            $this->assertDirectoryExists(app_path('Modules/'.$module), $module.' directory is missing');

            $provider = 'App\\Modules\\'.$module.'\\Providers\\'.$module.'ServiceProvider';

            $this->assertTrue(class_exists($provider), $provider.' is missing');
            $this->assertTrue(app()->providerIsLoaded($provider), $provider.' is not loaded');
        }
    }

    public function test_modules_register_their_own_configuration(): void
    {
        $this->assertSame('Core', config('core.name'));
        $this->assertSame('HR', config('hr.name'));
        $this->assertSame('DynamicModules', config('dynamic_modules.name'));
    }

    public function test_a_disabled_module_is_not_loaded(): void
    {
        $this->assertNotEmpty(config('modules.modules.Core'));
        $this->assertTrue((bool) config('modules.modules.Core.enabled'));
    }
}
