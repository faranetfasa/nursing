<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Insurance module web routes
|--------------------------------------------------------------------------
|
| Registered by InsuranceServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('insurance.route_prefix'))
    ->name('insurance.')
    ->group(function (): void {
        //
    });
