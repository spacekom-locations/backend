<?php

use App\Http\Controllers\Locations\LocationsController;
use App\Http\Controllers\Users\UsersController;
use Illuminate\Support\Facades\Route;

Route::prefix('authenticate')->group(function () {
    #create new role
    Route::post('/signup', [UsersController::class, 'signUp'])->name('users.authenticate.signup');
    Route::post('/login', [UsersController::class, 'login'])->name('users.authenticate.login');
});

Route::middleware('auth:sanctum', 'auth:users')->group(function () {
    Route::get('logout', [UsersController::class, 'logout'])->name('users.logout');
    Route::prefix('users')->group(function () {
        //The current connected user data
        Route::prefix('0')->group(function () {
            //update current user data
            Route::put('', [UsersController::class, 'update'])->name('users.update');

            //get current user personal access tokens
            Route::get('personal-access-tokens', [UsersController::class, 'getAllPersonalAccessToken'])
                ->name('users.personal_access_tokens.index');

            //add new location to the current user listing
            Route::post('locations', [LocationsController::class, 'store'])->name('locations.store');

            //index user locations
            Route::get('locations', [UsersController::class, 'indexLocations'])->name('users.locations.index');

            //show location
            Route::get('locations/{id}', [UsersController::class, 'showLocation'])->name('users.locations.show');

            //update location
            Route::put('locations/{id}', [LocationsController::class, 'update'])->name('locations.update');

            //add location images
            Route::post('locations/{id}/images', [UsersController::class, 'addLocationImages'])->name('users.locations.images.add');

            //delete location image
            Route::delete('locations/{id}/images', [UsersController::class, 'removeLocationImage'])->name('users.locations.images.delete');
        });

        //users data
        Route::get('{id}', [UsersController::class, 'show'])->name('users.show');
    });
});
