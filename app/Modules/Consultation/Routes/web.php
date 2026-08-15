<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Consultation module web routes
|--------------------------------------------------------------------------
|
| Registered by ConsultationServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('consultation.route_prefix'))
    ->name('consultation.')
    ->group(function (): void {
        //
    });
