<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Forms module web routes
|--------------------------------------------------------------------------
|
| Registered by FormsServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('forms.route_prefix'))
    ->name('forms.')
    ->group(function (): void {
        //
    });
