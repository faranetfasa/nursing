<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CRM module web routes
|--------------------------------------------------------------------------
|
| Registered by CRMServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('crm.route_prefix'))
    ->name('crm.')
    ->group(function (): void {
        //
    });
