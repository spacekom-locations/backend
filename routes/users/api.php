<?php

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
            Route::put('', [UsersController::class, 'update'])->name('users.update');
            Route::get('personal-access-tokens', [UsersController::class, 'getAllPersonalAccessToken'])
                ->name('users.personal_access_tokens.index');
        });

        //users data
        Route::get('{id}', [UsersController::class, 'show'])->name('users.show');
    });
});
