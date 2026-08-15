<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Nursing module web routes
|--------------------------------------------------------------------------
|
| Registered by NursingServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('nursing.route_prefix'))
    ->name('nursing.')
    ->group(function (): void {
        //
    });
