<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Medical module web routes
|--------------------------------------------------------------------------
|
| Registered by MedicalServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('medical.route_prefix'))
    ->name('medical.')
    ->group(function (): void {
        //
    });
