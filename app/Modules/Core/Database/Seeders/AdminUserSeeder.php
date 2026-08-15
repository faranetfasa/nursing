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
            'admin_password' => env('ADMIN_PASSWORD'),
        ], static::$data);

        $organization = Organization::query()->updateOrCreate(
            ['slug' => $this->slug((string) $data['organization_name'])],
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

        $attributes = [
            'organization_id' => $organization->id,
            'branch_id' => $branch->id,
            'name' => $data['admin_name'],
            'mobile' => $data['admin_mobile'],
            'email' => $data['admin_email'],
            'status' => User::STATUS_ACTIVE,
            'is_super_admin' => true,
            'email_verified_at' => now(),
            'password_changed_at' => now(),
        ];

        $existing = User::query()->where('username', $data['admin_username'])->exists();
        $password = (string) ($data['admin_password'] ?? '');

        /*
         * No hard coded credentials: without an explicit password a random one
         * is generated and printed once. An existing account keeps its password.
         */
        if ($password === '' && ! $existing) {
            $password = Str::password(16);

            $this->command?->warn('رمز عبور مدیر سیستم به‌صورت تصادفی ساخته شد: '.$password);
            $this->command?->warn('لطفاً پس از نخستین ورود آن را تغییر دهید.');
        }

        if ($password !== '') {
            $attributes['password'] = $password;
        }

        $admin = User::query()->updateOrCreate(['username' => $data['admin_username']], $attributes);

        $admin->syncRoles(['super-admin']);

        $organization->forceFill(['manager_id' => $admin->id])->save();
    }

    /** Str::slug() drops Persian characters, so a stable hash suffix is used instead. */
    private function slug(string $name): string
    {
        $slug = Str::slug($name);

        return $slug !== '' ? $slug : 'org-'.substr(md5($name), 0, 8);
    }
}
