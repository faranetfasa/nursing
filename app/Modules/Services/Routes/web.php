<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Services module web routes
|--------------------------------------------------------------------------
|
| Registered by ServicesServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('services.route_prefix'))
    ->name('services.')
    ->group(function (): void {
        //
    });
