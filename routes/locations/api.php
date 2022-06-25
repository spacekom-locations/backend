<?php

use App\Http\Controllers\Locations\LocationsController;
use Illuminate\Support\Facades\Route;

Route::prefix('locations')->group(function () {
    Route::get('search', [LocationsController::class, 'search'])->name('locations.search');
    Route::get('popular', [LocationsController::class, 'indexPopular'])->name('locations.popular');
    Route::get('{id}', [LocationsController::class, 'show'])->name('locations.show');
});
