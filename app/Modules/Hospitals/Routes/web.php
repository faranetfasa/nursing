<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Hospitals module web routes
|--------------------------------------------------------------------------
|
| Registered by HospitalsServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('hospitals.route_prefix'))
    ->name('hospitals.')
    ->group(function (): void {
        //
    });
