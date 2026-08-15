<?php

return [

    'name' => 'Core',
    'title' => 'هسته سیستم',
    'route_prefix' => '',
    'permission_prefix' => 'core',

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'login_fields' => ['username', 'mobile'],
        'max_attempts' => 5,
        'throttle_decay_seconds' => 60,
        'remember_me' => true,
        'password_min_length' => 8,
        'login_history_page_size' => 20,
    ],

    /*
    |--------------------------------------------------------------------------
    | RBAC
    |--------------------------------------------------------------------------
    |
    | Granular permissions are named "{group}.{action}". Groups mirror the
    | modules and their entities, actions are listed below.
    |
    */
    'rbac' => [
        'guard' => 'web',

        'actions' => ['view', 'create', 'edit', 'delete', 'approve', 'reject', 'export', 'print', 'assign', 'settings'],

        'action_labels' => [
            'view' => 'مشاهده',
            'create' => 'ایجاد',
            'edit' => 'ویرایش',
            'delete' => 'حذف',
            'approve' => 'تأیید',
            'reject' => 'رد',
            'export' => 'خروجی',
            'print' => 'چاپ',
            'assign' => 'تخصیص',
            'settings' => 'تنظیمات',
        ],

        /*
        | Permission groups seeded in this phase. Later phases add their own
        | groups from the module seeders.
        */
        'permission_groups' => [
            'core.users' => ['title' => 'کاربران', 'actions' => ['view', 'create', 'edit', 'delete', 'export']],
            'core.roles' => ['title' => 'نقش‌ها', 'actions' => ['view', 'create', 'edit', 'delete', 'assign']],
            'core.permissions' => ['title' => 'دسترسی‌ها', 'actions' => ['view', 'assign']],
            'core.organizations' => ['title' => 'سازمان', 'actions' => ['view', 'create', 'edit', 'delete']],
            'core.branches' => ['title' => 'شعبه‌ها', 'actions' => ['view', 'create', 'edit', 'delete']],
            'core.departments' => ['title' => 'دپارتمان‌ها', 'actions' => ['view', 'create', 'edit', 'delete']],
            'core.positions' => ['title' => 'سمت‌ها', 'actions' => ['view', 'create', 'edit', 'delete']],
            'core.settings' => ['title' => 'تنظیمات سیستم', 'actions' => ['view', 'edit', 'settings']],
            'core.audit_logs' => ['title' => 'گزارش عملیات', 'actions' => ['view', 'export']],
            'core.notifications' => ['title' => 'اطلاع‌رسانی‌ها', 'actions' => ['view', 'create', 'delete']],
            'core.dashboard' => ['title' => 'داشبورد', 'actions' => ['view']],
            'core.files' => ['title' => 'فایل‌های محرمانه', 'actions' => ['view', 'upload', 'delete']],
        ],

        /*
        | Default roles (specification item 16). "*" grants every permission.
        */
        'default_roles' => [
            'super-admin' => ['title' => 'مدیر ارشد سیستم', 'level' => 100, 'permissions' => '*'],
            'ceo' => ['title' => 'مدیرعامل', 'level' => 90, 'permissions' => ['core.dashboard.view', 'core.users.view', 'core.roles.view', 'core.organizations.view', 'core.branches.view', 'core.departments.view', 'core.positions.view', 'core.audit_logs.view', 'core.settings.view']],
            'admin' => ['title' => 'مدیر سیستم', 'level' => 85, 'permissions' => '*'],
            'hr-manager' => ['title' => 'مدیر منابع انسانی', 'level' => 70, 'permissions' => ['core.dashboard.view', 'core.users.view', 'core.users.create', 'core.users.edit', 'core.departments.view', 'core.positions.view']],
            'finance-manager' => ['title' => 'مدیر مالی', 'level' => 70, 'permissions' => ['core.dashboard.view', 'core.users.view']],
            'nursing-manager' => ['title' => 'مدیر پرستاری', 'level' => 70, 'permissions' => ['core.dashboard.view', 'core.users.view']],
            'branch-manager' => ['title' => 'مدیر شعبه', 'level' => 60, 'permissions' => ['core.dashboard.view', 'core.users.view', 'core.branches.view', 'core.departments.view']],
            'supervisor' => ['title' => 'سرپرست', 'level' => 50, 'permissions' => ['core.dashboard.view', 'core.users.view']],
            'operator' => ['title' => 'کارشناس/اپراتور', 'level' => 30, 'permissions' => ['core.dashboard.view']],
            'nurse' => ['title' => 'پرستار', 'level' => 20, 'permissions' => ['core.dashboard.view']],
            'doctor' => ['title' => 'پزشک', 'level' => 20, 'permissions' => ['core.dashboard.view']],
            'patient' => ['title' => 'بیمار/مددجو', 'level' => 10, 'permissions' => []],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit trail
    |--------------------------------------------------------------------------
    */
    'audit' => [
        'enabled' => env('CORE_AUDIT_ENABLED', true),
        'hidden_attributes' => ['password', 'remember_token', 'api_token'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Settings and their fallbacks
    |--------------------------------------------------------------------------
    |
    | Values are read through App\Modules\Core\Services\SettingService, which
    | falls back to the defaults below when a row does not exist yet.
    |
    */
    'settings' => [
        'cache_ttl' => 3600,

        'defaults' => [
            'general.app_name' => 'سامانه مدیریت خدمات پرستاری',
            'general.organization_name' => '',
            'general.timezone' => 'Asia/Tehran',
            'general.locale' => 'fa',
            'general.calendar' => 'jalali',
            'general.currency' => 'IRR',
            'general.support_phone' => '',
            'appearance.primary_color' => '#0d9488',
            'appearance.secondary_color' => '#1e293b',
            'appearance.default_theme' => 'light',
            'appearance.font_family' => 'Vazirmatn',
            'appearance.logo_path' => null,
            'appearance.favicon_path' => null,
            'appearance.sidebar_collapsed' => false,
            'security.session_lifetime' => 120,
            'security.force_password_change_days' => 0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Secure file storage (files must never live in public/)
    |--------------------------------------------------------------------------
    */
    'storage' => [
        'disk' => env('CORE_SECURE_DISK', 'secure'),
        'permission' => 'core.files.view',
        'max_upload_size' => 10240,
        'allowed_mimes' => ['pdf', 'jpg', 'jpeg', 'png', 'webp', 'doc', 'docx', 'xls', 'xlsx', 'zip'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Installer
    |--------------------------------------------------------------------------
    */
    'installer' => [
        'lock_file' => env('INSTALLER_LOCK_FILE', 'installed.lock'),
        'url' => env('INSTALLER_URL', '/install.php'),
        'enabled' => env('INSTALLER_ENABLED', true),
    ],

];
