<?php

namespace App\Modules\Core\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    protected $fillable = [
        'name',
        'guard_name',
        'display_name',
        'group',
        'action',
        'description',
    ];

    public function label(): string
    {
        return $this->display_name ?: $this->name;
    }
}
