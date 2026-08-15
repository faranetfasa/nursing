<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Communication module web routes
|--------------------------------------------------------------------------
|
| Registered by CommunicationServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('communication.route_prefix'))
    ->name('communication.')
    ->group(function (): void {
        //
    });
