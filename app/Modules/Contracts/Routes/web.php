<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Contracts module web routes
|--------------------------------------------------------------------------
|
| Registered by ContractsServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('contracts.route_prefix'))
    ->name('contracts.')
    ->group(function (): void {
        //
    });
