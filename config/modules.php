<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Module registry
    |--------------------------------------------------------------------------
    |
    | Every module lives in app/Modules/{name} and is bootstrapped by its own
    | service provider (App\Modules\{name}\Providers\{name}ServiceProvider).
    | "Core" must stay first and enabled: the rest of the platform depends on
    | its authentication, RBAC and settings services.
    |
    | phase: 1 = implemented in the foundation phase, 2+ = planned (see
    | docs/roadmap.md). Only structure and the service provider exist for the
    | modules of the later phases.
    |
    */

    'modules' => [
        'Core' => ['name' => 'Core', 'title' => 'هسته سیستم', 'enabled' => true, 'phase' => 1],
        'HR' => ['name' => 'HR', 'title' => 'منابع انسانی', 'enabled' => true, 'phase' => 2],
        'Patients' => ['name' => 'Patients', 'title' => 'بیماران', 'enabled' => true, 'phase' => 2],
        'CRM' => ['name' => 'CRM', 'title' => 'ارتباط با مشتری', 'enabled' => true, 'phase' => 2],
        'Services' => ['name' => 'Services', 'title' => 'خدمات', 'enabled' => true, 'phase' => 2],
        'Nursing' => ['name' => 'Nursing', 'title' => 'پرستاری', 'enabled' => true, 'phase' => 2],
        'Medical' => ['name' => 'Medical', 'title' => 'پزشکی', 'enabled' => true, 'phase' => 3],
        'Contracts' => ['name' => 'Contracts', 'title' => 'قراردادها', 'enabled' => true, 'phase' => 3],
        'Finance' => ['name' => 'Finance', 'title' => 'مالی', 'enabled' => true, 'phase' => 3],
        'Payroll' => ['name' => 'Payroll', 'title' => 'حقوق و دستمزد', 'enabled' => true, 'phase' => 3],
        'Insurance' => ['name' => 'Insurance', 'title' => 'بیمه', 'enabled' => true, 'phase' => 3],
        'Pharmacy' => ['name' => 'Pharmacy', 'title' => 'دارویی', 'enabled' => true, 'phase' => 4],
        'Inventory' => ['name' => 'Inventory', 'title' => 'انبار', 'enabled' => true, 'phase' => 4],
        'Assets' => ['name' => 'Assets', 'title' => 'اموال و تجهیزات', 'enabled' => true, 'phase' => 4],
        'Hospitals' => ['name' => 'Hospitals', 'title' => 'بیمارستان‌ها', 'enabled' => true, 'phase' => 4],
        'Ambulance' => ['name' => 'Ambulance', 'title' => 'آمبولانس', 'enabled' => true, 'phase' => 4],
        'Dispatch' => ['name' => 'Dispatch', 'title' => 'اعزام', 'enabled' => true, 'phase' => 4],
        'Automation' => ['name' => 'Automation', 'title' => 'اتوماسیون', 'enabled' => true, 'phase' => 5],
        'Communication' => ['name' => 'Communication', 'title' => 'ارتباطات', 'enabled' => true, 'phase' => 5],
        'Support' => ['name' => 'Support', 'title' => 'پشتیبانی', 'enabled' => true, 'phase' => 5],
        'Satisfaction' => ['name' => 'Satisfaction', 'title' => 'رضایت‌سنجی', 'enabled' => true, 'phase' => 5],
        'Consultation' => ['name' => 'Consultation', 'title' => 'مشاوره', 'enabled' => true, 'phase' => 5],
        'Forms' => ['name' => 'Forms', 'title' => 'فرم‌ساز', 'enabled' => true, 'phase' => 6],
        'DynamicModules' => ['name' => 'DynamicModules', 'title' => 'ماژول‌ساز پویا', 'enabled' => true, 'phase' => 6],
    ],

];
