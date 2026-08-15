<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Finance module web routes
|--------------------------------------------------------------------------
|
| Registered by FinanceServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('finance.route_prefix'))
    ->name('finance.')
    ->group(function (): void {
        //
    });
