<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Satisfaction module web routes
|--------------------------------------------------------------------------
|
| Registered by SatisfactionServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('satisfaction.route_prefix'))
    ->name('satisfaction.')
    ->group(function (): void {
        //
    });
