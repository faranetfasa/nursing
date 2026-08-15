<?php

namespace App\Modules\Core\Support;

/**
 * Sidebar definition. Later phases append their own entries per module; the
 * permission key of each item is checked before it is rendered.
 */
class Menu
{
    /** @return array<int, array<string, string|null>> */
    public static function items(): array
    {
        return [
            [
                'title' => 'داشبورد',
                'icon' => '▦',
                'url' => route('core.dashboard'),
                'route_pattern' => 'core.dashboard',
                'permission' => 'core.dashboard.view',
            ],
            [
                'title' => 'تاریخچه ورود من',
                'icon' => '⟳',
                'url' => route('core.account.login-history'),
                'route_pattern' => 'core.account.login-history',
                'permission' => null,
            ],
            [
                'title' => 'گزارش عملیات سیستم',
                'icon' => '☰',
                'url' => route('core.audit-logs.index'),
                'route_pattern' => 'core.audit-logs.*',
                'permission' => 'core.audit_logs.view',
            ],
        ];
    }
}
