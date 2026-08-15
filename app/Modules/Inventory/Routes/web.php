<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Inventory module web routes
|--------------------------------------------------------------------------
|
| Registered by InventoryServiceProvider. The module itself is implemented in
| a later phase (see docs/roadmap.md); only its structure exists today.
|
*/

Route::middleware(['auth', 'installed'])
    ->prefix(config('inventory.route_prefix'))
    ->name('inventory.')
    ->group(function (): void {
        //
    });
