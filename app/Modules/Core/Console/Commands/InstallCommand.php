<?php

namespace App\Modules\Core\Console\Commands;

use App\Modules\Core\Database\Seeders\AdminUserSeeder;
use App\Modules\Core\Database\Seeders\DemoDataSeeder;
use App\Modules\Core\Database\Seeders\PermissionSeeder;
use App\Modules\Core\Database\Seeders\RoleSeeder;
use App\Modules\Core\Database\Seeders\SettingSeeder;
use App\Modules\Core\Services\InstallerService;
use App\Modules\Core\Services\SettingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

/**
 * Non interactive installation used by the graphical installer (and available
 * for command line installations). Runs migrations, seeds the Core data,
 * creates the storage link and writes the installation lock file.
 */
class InstallCommand extends Command
{
    protected $signature = 'nursing:install
        {--org-name= : Organisation name}
        {--org-phone= }
        {--org-city= }
        {--org-address= }
        {--app-name= : Interface title}
        {--primary-color= }
        {--timezone= }
        {--admin-name= }
        {--admin-username= }
        {--admin-mobile= }
        {--admin-email= }
        {--admin-password= : Prefer NURSING_ADMIN_PASSWORD, command lines are world readable}
        {--demo : Also install demo data}
        {--fresh : Drop existing tables before migrating}
        {--force : Run in production without confirmation}';

    protected $description = 'Install the platform: migrations, base data, storage link and lock file';

    public const PASSWORD_ENV = 'NURSING_ADMIN_PASSWORD';

    public function handle(InstallerService $installer, SettingService $settings): int
    {
        $this->components->info('اجرای مهاجرت‌های پایگاه داده');

        Artisan::call($this->option('fresh') ? 'migrate:fresh' : 'migrate', ['--force' => true], $this->getOutput());

        $this->components->info('ثبت دسترسی‌ها، نقش‌ها و تنظیمات پایه');

        foreach ([PermissionSeeder::class, RoleSeeder::class, SettingSeeder::class] as $seeder) {
            $this->callSilently('db:seed', ['--class' => $seeder, '--force' => true]);
        }

        $settings->setMany(array_filter([
            'general.app_name' => $this->option('app-name'),
            'general.organization_name' => $this->option('org-name'),
            'general.timezone' => $this->option('timezone'),
            'appearance.primary_color' => $this->option('primary-color'),
        ], static fn ($value): bool => filled($value)));

        $this->components->info('ایجاد سازمان و مدیر سیستم');

        AdminUserSeeder::$data = array_filter([
            'organization_name' => $this->option('org-name'),
            'organization_phone' => $this->option('org-phone'),
            'organization_city' => $this->option('org-city'),
            'organization_address' => $this->option('org-address'),
            'admin_name' => $this->option('admin-name'),
            'admin_username' => $this->option('admin-username'),
            'admin_mobile' => $this->option('admin-mobile'),
            'admin_email' => $this->option('admin-email'),
            'admin_password' => $this->adminPassword(),
        ], static fn ($value): bool => filled($value));

        $this->callSilently('db:seed', ['--class' => AdminUserSeeder::class, '--force' => true]);

        AdminUserSeeder::$data = [];

        if ($this->option('demo')) {
            $this->components->info('ایجاد داده‌های نمونه');
            $this->callSilently('db:seed', ['--class' => DemoDataSeeder::class, '--force' => true]);
        }

        $this->components->info('ایجاد پیوند storage و پاک‌سازی کش');

        $this->createStorageLink();
        $this->callSilently('config:clear');
        $this->callSilently('cache:clear');
        $this->callSilently('view:clear');

        file_put_contents($installer->lockPath(), json_encode([
            'version' => (string) config('app.version', '1.0.0'),
            'installed_at' => now()->toIso8601String(),
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        $this->components->info('نصب با موفقیت انجام شد.');

        return self::SUCCESS;
    }

    /**
     * The environment variable is preferred over the option because process
     * command lines are readable by every local user (/proc/<pid>/cmdline).
     */
    private function adminPassword(): ?string
    {
        $password = getenv(self::PASSWORD_ENV);

        return $password === false || $password === '' ? $this->option('admin-password') : $password;
    }

    private function createStorageLink(): void
    {
        if (is_link(public_path('storage')) || is_dir(public_path('storage'))) {
            return;
        }

        try {
            $this->callSilently('storage:link');
        } catch (\Throwable $exception) {
            $this->components->warn('ایجاد پیوند storage ناموفق بود: '.$exception->getMessage());
        }
    }
}
