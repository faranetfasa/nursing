<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * RBAC tables (spatie/laravel-permission compatible) using the table and column
 * names required by the specification: roles, permissions, user_roles,
 * role_permissions. A user may hold several roles at the same time.
 */
return new class extends Migration
{
    public function up(): void
    {
        $tables = config('permission.table_names');
        $columns = config('permission.column_names');
        $pivotRole = $columns['role_pivot_key'] ?? 'role_id';
        $pivotPermission = $columns['permission_pivot_key'] ?? 'permission_id';
        $morphKey = $columns['model_morph_key'] ?? 'user_id';

        Schema::create($tables['permissions'], function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->string('display_name')->nullable();
            $table->string('group', 100)->nullable()->comment('module or feature the permission belongs to');
            $table->string('action', 50)->nullable()->comment('view, create, edit, delete, approve, ...');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
            $table->index('group');
        });

        Schema::create($tables['roles'], function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('guard_name')->default('web');
            $table->string('display_name')->nullable();
            $table->text('description')->nullable();
            $table->unsignedTinyInteger('level')->default(10);
            $table->boolean('is_system')->default(false)->comment('system roles can not be deleted');
            $table->timestamps();

            $table->unique(['name', 'guard_name']);
        });

        Schema::create($tables['model_has_permissions'], function (Blueprint $table) use ($tables, $pivotPermission, $morphKey): void {
            $table->unsignedBigInteger($pivotPermission);
            $table->string('model_type');
            $table->unsignedBigInteger($morphKey);

            $table->index([$morphKey, 'model_type'], 'user_permissions_model_id_model_type_index');
            $table->foreign($pivotPermission)->references('id')->on($tables['permissions'])->cascadeOnDelete();
            $table->primary([$pivotPermission, $morphKey, 'model_type'], 'user_permissions_permission_model_type_primary');
        });

        Schema::create($tables['model_has_roles'], function (Blueprint $table) use ($tables, $pivotRole, $morphKey): void {
            $table->unsignedBigInteger($pivotRole);
            $table->string('model_type');
            $table->unsignedBigInteger($morphKey);

            $table->index([$morphKey, 'model_type'], 'user_roles_model_id_model_type_index');
            $table->foreign($pivotRole)->references('id')->on($tables['roles'])->cascadeOnDelete();
            $table->primary([$pivotRole, $morphKey, 'model_type'], 'user_roles_role_model_type_primary');
        });

        Schema::create($tables['role_has_permissions'], function (Blueprint $table) use ($tables, $pivotRole, $pivotPermission): void {
            $table->unsignedBigInteger($pivotPermission);
            $table->unsignedBigInteger($pivotRole);

            $table->foreign($pivotPermission)->references('id')->on($tables['permissions'])->cascadeOnDelete();
            $table->foreign($pivotRole)->references('id')->on($tables['roles'])->cascadeOnDelete();
            $table->primary([$pivotPermission, $pivotRole], 'role_permissions_permission_id_role_id_primary');
        });

        app('cache')->store(config('permission.cache.store') !== 'default' ? config('permission.cache.store') : null)
            ->forget(config('permission.cache.key'));
    }

    public function down(): void
    {
        $tables = config('permission.table_names');

        Schema::dropIfExists($tables['role_has_permissions']);
        Schema::dropIfExists($tables['model_has_roles']);
        Schema::dropIfExists($tables['model_has_permissions']);
        Schema::dropIfExists($tables['roles']);
        Schema::dropIfExists($tables['permissions']);
    }
};
