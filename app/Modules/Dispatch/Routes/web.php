<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Dispatch module web routes
|--------------------------------------------------------------------------
|
| Registered by DispatchServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('dispatch.route_prefix'))
    ->name('dispatch.')
    ->group(function (): void {
        //
    });
