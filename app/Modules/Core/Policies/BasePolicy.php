<?php

namespace App\Modules\Core\Policies;

use App\Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Model;

/**
 * Maps the standard CRUD abilities onto the granular permissions of a group
 * ("core.users.view", "core.users.delete", ...). Concrete policies only declare
 * their permission group.
 */
abstract class BasePolicy
{
    protected string $group = '';

    public function viewAny(User $user): bool
    {
        return $this->allows($user, 'view');
    }

    public function view(User $user, Model $model): bool
    {
        return $this->allows($user, 'view');
    }

    public function create(User $user): bool
    {
        return $this->allows($user, 'create');
    }

    public function update(User $user, Model $model): bool
    {
        return $this->allows($user, 'edit');
    }

    public function delete(User $user, Model $model): bool
    {
        return $this->allows($user, 'delete');
    }

    public function restore(User $user, Model $model): bool
    {
        return $this->allows($user, 'edit');
    }

    public function forceDelete(User $user, Model $model): bool
    {
        return $this->allows($user, 'delete');
    }

    public function approve(User $user, Model $model): bool
    {
        return $this->allows($user, 'approve');
    }

    public function export(User $user): bool
    {
        return $this->allows($user, 'export');
    }

    protected function allows(User $user, string $action): bool
    {
        return $user->is_super_admin || $user->can($this->group.'.'.$action);
    }
}
