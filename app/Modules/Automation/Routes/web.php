<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Automation module web routes
|--------------------------------------------------------------------------
|
| Registered by AutomationServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('automation.route_prefix'))
    ->name('automation.')
    ->group(function (): void {
        //
    });
