<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Assets module web routes
|--------------------------------------------------------------------------
|
| Registered by AssetsServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('assets.route_prefix'))
    ->name('assets.')
    ->group(function (): void {
        //
    });
