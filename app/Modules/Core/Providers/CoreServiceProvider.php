<?php

namespace App\Modules\Core\Providers;

use App\Modules\Core\Console\Commands\InstallCommand;
use App\Modules\Core\Http\Livewire\DashboardStats;
use App\Modules\Core\Models\Branch;
use App\Modules\Core\Models\Department;
use App\Modules\Core\Models\Organization;
use App\Modules\Core\Models\Permission;
use App\Modules\Core\Models\Position;
use App\Modules\Core\Models\Role;
use App\Modules\Core\Models\Setting;
use App\Modules\Core\Models\User;
use App\Modules\Core\Policies\BranchPolicy;
use App\Modules\Core\Policies\DepartmentPolicy;
use App\Modules\Core\Policies\OrganizationPolicy;
use App\Modules\Core\Policies\PermissionPolicy;
use App\Modules\Core\Policies\PositionPolicy;
use App\Modules\Core\Policies\RolePolicy;
use App\Modules\Core\Policies\SettingPolicy;
use App\Modules\Core\Policies\UserPolicy;
use App\Modules\Core\Services\AuditService;
use App\Modules\Core\Services\AuthService;
use App\Modules\Core\Services\InstallerService;
use App\Modules\Core\Services\PermissionService;
use App\Modules\Core\Services\SettingService;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Livewire\Livewire;

class CoreServiceProvider extends BaseModuleServiceProvider
{
    protected string $module = 'Core';

    /** @var array<class-string, class-string> */
    private array $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Organization::class => OrganizationPolicy::class,
        Branch::class => BranchPolicy::class,
        Department::class => DepartmentPolicy::class,
        Position::class => PositionPolicy::class,
        Setting::class => SettingPolicy::class,
    ];

    public function register(): void
    {
        parent::register();

        $this->app->singleton(SettingService::class);
        $this->app->singleton(AuditService::class);
        $this->app->singleton(PermissionService::class);
        $this->app->singleton(AuthService::class);
        $this->app->singleton(InstallerService::class);
    }

    public function boot(): void
    {
        parent::boot();

        $this->registerPolicies();
        $this->registerSuperAdmin();
        $this->registerLivewireComponents();
        $this->shareBranding();

        if ($this->app->runningInConsole()) {
            $this->commands([InstallCommand::class]);
        }
    }

    private function registerLivewireComponents(): void
    {
        Livewire::component('core.dashboard-stats', DashboardStats::class);
    }

    private function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }

    /** A super admin passes every gate without needing explicit permissions. */
    private function registerSuperAdmin(): void
    {
        Gate::before(static function ($user): ?bool {
            return $user instanceof User && $user->is_super_admin ? true : null;
        });
    }

    /** Title, logo and colours come from the settings table, never hard coded. */
    private function shareBranding(): void
    {
        View::composer(['core::layouts.*', 'core::auth.*', 'core::dashboard.*'], function ($view): void {
            $view->with('branding', app(SettingService::class)->branding());
        });
    }
}
