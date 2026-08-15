<?php

use App\Modules\Core\Http\Controllers\AuditLogController;
use App\Modules\Core\Http\Controllers\Auth\ForgotPasswordController;
use App\Modules\Core\Http\Controllers\Auth\LoginController;
use App\Modules\Core\Http\Controllers\Auth\ResetPasswordController;
use App\Modules\Core\Http\Controllers\DashboardController;
use App\Modules\Core\Http\Controllers\LoginHistoryController;
use App\Modules\Core\Http\Controllers\SecureFileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Core module web routes
|--------------------------------------------------------------------------
*/

Route::redirect('/', '/dashboard')->name('core.home');

Route::middleware('guest')->group(function (): void {
    Route::get('login', [LoginController::class, 'create'])->name('core.login');
    Route::post('login', [LoginController::class, 'store'])->name('core.login.store');

    Route::get('forgot-password', [ForgotPasswordController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [ForgotPasswordController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::post('logout', [LoginController::class, 'destroy'])->name('core.logout');

    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:core.dashboard.view')
        ->name('core.dashboard');

    Route::get('account/login-history', [LoginHistoryController::class, 'index'])->name('core.account.login-history');

    Route::get('files/{path}', [SecureFileController::class, 'show'])
        ->where('path', '.*')
        ->name('core.files.show');

    Route::get('system/audit-logs', [AuditLogController::class, 'index'])
        ->middleware('permission:core.audit_logs.view')
        ->name('core.audit-logs.index');
});
