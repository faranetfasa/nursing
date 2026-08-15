<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Pharmacy module web routes
|--------------------------------------------------------------------------
|
| Registered by PharmacyServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('pharmacy.route_prefix'))
    ->name('pharmacy.')
    ->group(function (): void {
        //
    });
