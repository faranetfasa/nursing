<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Support module web routes
|--------------------------------------------------------------------------
|
| Registered by SupportServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('support.route_prefix'))
    ->name('support.')
    ->group(function (): void {
        //
    });
