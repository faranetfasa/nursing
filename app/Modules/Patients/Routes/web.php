<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Patients module web routes
|--------------------------------------------------------------------------
|
| Registered by PatientsServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('patients.route_prefix'))
    ->name('patients.')
    ->group(function (): void {
        //
    });
