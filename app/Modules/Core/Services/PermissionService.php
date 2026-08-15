<?php

namespace App\Modules\Core\Services;

use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\User;
use Illuminate\Support\Collection;

/**
 * RBAC helper around spatie/laravel-permission: granular permissions are named
 * "{group}.{action}" (e.g. hr.employees.approve) and grouped per module so the
 * role screens of later phases can render them without extra queries.
 */
class PermissionService
{
    public function __construct(private readonly AuditService $audit) {}

    /** @return array<int, string> */
    public function actions(): array
    {
        return config('core.rbac.actions', ['view', 'create', 'edit', 'delete']);
    }

    public function permissionName(string $group, string $action): string
    {
        return $group.'.'.$action;
    }

    /** Creates the permission if missing and returns it. */
    public function ensurePermission(string $group, string $action, ?string $displayName = null): Permission
    {
        return Permission::query()->firstOrCreate(
            ['name' => $this->permissionName($group, $action), 'guard_name' => config('core.rbac.guard', 'web')],
            [
                'group' => $group,
                'action' => $action,
                'display_name' => $displayName ?? $this->actionLabel($action).' '.$group,
            ]
        );
    }

    /** @param array<int, string> $permissions */
    public function syncRolePermissions(Role $role, array $permissions): Role
    {
        $before = $role->permissions->pluck('name')->all();

        $role->syncPermissions($permissions);

        $this->audit->logModel('permissions_synced', $role, [
            'description' => 'به‌روزرسانی دسترسی‌های نقش '.$role->label(),
            'old_values' => ['permissions' => $before],
            'new_values' => ['permissions' => $permissions],
        ]);

        return $role->refresh();
    }

    /** @param array<int, string> $roles */
    public function syncUserRoles(User $user, array $roles): User
    {
        $before = $user->roles->pluck('name')->all();

        $user->syncRoles($roles);

        $this->audit->logModel('roles_synced', $user, [
            'description' => 'به‌روزرسانی نقش‌های کاربر '.$user->username,
            'old_values' => ['roles' => $before],
            'new_values' => ['roles' => $roles],
        ]);

        return $user->refresh();
    }

    /** @return Collection<string, Collection<int, Permission>> */
    public function grouped(): Collection
    {
        return Permission::query()->orderBy('group')->orderBy('name')->get()->groupBy('group');
    }

    public function actionLabel(string $action): string
    {
        return config('core.rbac.action_labels.'.$action, $action);
    }

    public function userCan(User $user, string $permission): bool
    {
        return $user->is_super_admin || $user->can($permission);
    }
}
