<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| DynamicModules module web routes
|--------------------------------------------------------------------------
|
| Registered by DynamicModulesServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('dynamic_modules.route_prefix'))
    ->name('dynamic_modules.')
    ->group(function (): void {
        //
    });
