<?php

namespace Tests\Feature;

use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\User;
use App\Modules\Core\Services\SettingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_organization_settings_override_the_global_value_without_leaking(): void
    {
        $first = Organization::query()->create(['name' => 'سازمان اول', 'slug' => 'first']);
        $second = Organization::query()->create(['name' => 'سازمان دوم', 'slug' => 'second']);

        $settings = app(SettingService::class);
        $settings->set('general.app_name', 'سراسری');
        $settings->set('general.app_name', 'اول', 'string', ['organization_id' => $first->id]);

        $this->assertSame('سراسری', $settings->get('general.app_name', null, null));
        $this->assertSame('اول', $settings->get('general.app_name', null, $first->id));
        $this->assertSame('سراسری', $settings->get('general.app_name', null, $second->id));
    }

    public function test_the_current_scope_follows_the_authenticated_user(): void
    {
        $organization = Organization::query()->create(['name' => 'سازمان', 'slug' => 'org']);
        $user = User::factory()->create(['organization_id' => $organization->id]);

        $settings = app(SettingService::class);
        $settings->set('general.app_name', 'سراسری');
        $settings->set('general.app_name', 'سازمانی', 'string', ['organization_id' => $organization->id]);

        $this->assertSame('سراسری', $settings->get('general.app_name'));

        $this->actingAs($user);
        $settings->flush();

        $this->assertSame('سازمانی', $settings->get('general.app_name'));
    }

    public function test_forget_only_removes_the_requested_scope(): void
    {
        $organization = Organization::query()->create(['name' => 'سازمان', 'slug' => 'org']);

        $settings = app(SettingService::class);
        $settings->set('general.app_name', 'سراسری');
        $settings->set('general.app_name', 'سازمانی', 'string', ['organization_id' => $organization->id]);

        $settings->forget('general.app_name', $organization->id);

        $this->assertDatabaseHas('settings', ['organization_id' => null, 'key' => 'app_name']);
        $this->assertDatabaseMissing('settings', ['organization_id' => $organization->id, 'key' => 'app_name']);
        $this->assertSame('سراسری', $settings->get('general.app_name', null, $organization->id));
    }

    public function test_a_missing_setting_falls_back_to_the_configuration(): void
    {
        config()->set('core.settings.defaults', ['general.app_name' => 'پیش‌فرض']);

        $this->assertSame('پیش‌فرض', app(SettingService::class)->get('general.app_name'));
    }
}
