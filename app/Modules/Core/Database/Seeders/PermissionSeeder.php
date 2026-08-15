<?php

namespace App\Modules\Core\Database\Seeders;

use App\Modules\Core\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

/**
 * Creates the granular permissions of the Core module from
 * config('core.rbac.permission_groups'). Later phases add their own groups.
 */
class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = (string) config('core.rbac.guard', 'web');

        foreach ((array) config('core.rbac.permission_groups', []) as $group => $definition) {
            foreach ($definition['actions'] as $action) {
                Permission::query()->updateOrCreate(
                    ['name' => $group.'.'.$action, 'guard_name' => $guard],
                    [
                        'group' => $group,
                        'action' => $action,
                        'display_name' => config('core.rbac.action_labels.'.$action, $action).' '.$definition['title'],
                    ]
                );
            }
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
