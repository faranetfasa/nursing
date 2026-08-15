<?php

namespace Tests\Feature;

use App\Modules\Core\Database\Seeders\PermissionSeeder;
use App\Modules\Core\Database\Seeders\RoleSeeder;
use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionSeeder::class);
        $this->seed(RoleSeeder::class);
    }

    public function test_granular_permissions_and_default_roles_are_seeded(): void
    {
        $this->assertTrue(Permission::query()->where('name', 'core.users.create')->exists());
        $this->assertTrue(Permission::query()->where('name', 'core.audit_logs.export')->exists());

        foreach (['super-admin', 'ceo', 'hr-manager', 'nurse'] as $role) {
            $this->assertTrue(Role::query()->where('name', $role)->exists(), $role.' role is missing');
        }

        $this->assertTrue(Role::query()->where('name', 'super-admin')->first()->permissions()->count() > 0);
    }

    public function test_a_user_can_hold_several_roles(): void
    {
        $user = User::factory()->create();
        $user->syncRoles(['hr-manager', 'branch-manager']);

        $this->assertEqualsCanonicalizing(['hr-manager', 'branch-manager'], $user->getRoleNames()->all());
        $this->assertTrue($user->can('core.users.create'));
        $this->assertTrue($user->can('core.branches.view'));
    }

    public function test_permission_middleware_blocks_a_user_without_the_permission(): void
    {
        $user = User::factory()->create();
        $user->assignRole('nurse');

        $this->actingAs($user)->get('/system/audit-logs')->assertForbidden();
        $this->assertDatabaseHas('audit_logs', ['event' => 'permission_denied']);
    }

    public function test_permission_middleware_allows_a_user_with_the_permission(): void
    {
        $user = User::factory()->create();
        $user->assignRole('ceo');

        $this->actingAs($user)->get('/system/audit-logs')->assertOk();
    }

    public function test_super_admin_passes_every_gate(): void
    {
        $user = User::factory()->superAdmin()->create();

        $this->assertTrue($user->can('core.settings.settings'));
        $this->assertTrue($user->can('anything.not.even.seeded'));
        $this->actingAs($user)->get('/system/audit-logs')->assertOk();
    }

    public function test_policies_use_the_granular_permissions(): void
    {
        $user = User::factory()->create();
        $user->assignRole('hr-manager');

        $this->assertTrue($user->can('viewAny', User::class));
        $this->assertTrue($user->can('create', User::class));
        $this->assertFalse($user->can('delete', User::factory()->create()));
    }

    public function test_dashboard_requires_the_dashboard_permission(): void
    {
        $withoutPermission = User::factory()->create();
        $withoutPermission->assignRole('patient');

        $this->actingAs($withoutPermission)->get('/dashboard')->assertForbidden();

        $withPermission = User::factory()->create();
        $withPermission->assignRole('operator');

        $this->actingAs($withPermission)->get('/dashboard')->assertOk();
    }
}
