<?php

namespace App\Modules\Core\Database\Seeders;

use App\Modules\Core\Models\Branch;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\User;
use App\Modules\Core\Services\SettingService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Default organisation, main branch and super administrator. The installer
 * passes the real values through the nursing:install command; the defaults below
 * are only used for a bare "db:seed" during development.
 */
class AdminUserSeeder extends Seeder
{
    /** @var array<string, mixed> */
    public static array $data = [];

    public function run(): void
    {
        $data = array_merge([
            'organization_name' => app(SettingService::class)->get('general.app_name', config('app.name')),
            'organization_phone' => null,
            'organization_city' => null,
            'organization_address' => null,
            'admin_name' => 'مدیر سیستم',
            'admin_username' => env('ADMIN_USERNAME', 'admin'),
            'admin_mobile' => env('ADMIN_MOBILE', '09120000000'),
            'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),
            'admin_password' => env('ADMIN_PASSWORD', 'Admin@12345'),
        ], static::$data);

        $organization = Organization::query()->firstOrCreate(
            ['slug' => Str::slug($data['organization_name']) ?: 'organization'],
            [
                'name' => $data['organization_name'],
                'phone' => $data['organization_phone'],
                'city' => $data['organization_city'],
                'address' => $data['organization_address'],
                'is_active' => true,
            ]
        );

        $branch = Branch::query()->firstOrCreate(
            ['organization_id' => $organization->id, 'code' => 'MAIN'],
            [
                'name' => 'شعبه مرکزی',
                'is_main' => true,
                'is_active' => true,
                'city' => $data['organization_city'],
                'address' => $data['organization_address'],
            ]
        );

        $admin = User::query()->updateOrCreate(
            ['username' => $data['admin_username']],
            [
                'organization_id' => $organization->id,
                'branch_id' => $branch->id,
                'name' => $data['admin_name'],
                'mobile' => $data['admin_mobile'],
                'email' => $data['admin_email'],
                'password' => $data['admin_password'],
                'status' => User::STATUS_ACTIVE,
                'is_super_admin' => true,
                'email_verified_at' => now(),
                'password_changed_at' => now(),
            ]
        );

        $admin->syncRoles(['super-admin']);

        $organization->forceFill(['manager_id' => $admin->id])->save();
    }
}
