<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Payroll module web routes
|--------------------------------------------------------------------------
|
| Registered by PayrollServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('payroll.route_prefix'))
    ->name('payroll.')
    ->group(function (): void {
        //
    });
