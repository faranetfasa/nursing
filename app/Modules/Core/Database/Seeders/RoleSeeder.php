<?php

namespace App\Modules\Core\Database\Seeders;

use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Default roles of specification item 16 with their permissions.
 */
class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $guard = (string) config('core.rbac.guard', 'web');
        $allPermissions = Permission::query()->pluck('name')->all();

        foreach ((array) config('core.rbac.default_roles', []) as $name => $definition) {
            $role = Role::query()->updateOrCreate(
                ['name' => $name, 'guard_name' => $guard],
                [
                    'display_name' => $definition['title'],
                    'level' => $definition['level'] ?? 10,
                    'is_system' => true,
                ]
            );

            $permissions = $definition['permissions'] === '*'
                ? $allPermissions
                : array_values(array_intersect($allPermissions, (array) $definition['permissions']));

            $role->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
