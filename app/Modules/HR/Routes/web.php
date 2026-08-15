<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| HR module web routes
|--------------------------------------------------------------------------
|
| Registered by HRServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('hr.route_prefix'))
    ->name('hr.')
    ->group(function (): void {
        //
    });
