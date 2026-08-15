<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Ambulance module web routes
|--------------------------------------------------------------------------
|
| Registered by AmbulanceServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('ambulance.route_prefix'))
    ->name('ambulance.')
    ->group(function (): void {
        //
    });
