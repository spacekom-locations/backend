<?php

use App\Http\Controllers\Supervisors\SupervisorsController;
use Illuminate\Support\Facades\Route;

Route::prefix('supervisors')->group(function () {
    Route::post('/login', [SupervisorsController::class, 'login'])->name('supervisors.login');
    
    Route::middleware('auth:sanctum', 'auth:supervisors')->group(function () {
        #load system permissions
        Route::get('/permissions', [SupervisorsController::class, 'getSystemPermissions'])->name('supervisors.systemPermissions');

        #logout supervisor
        Route::get('/logout', [SupervisorsController::class, 'logout'])->name('supervisors.logout');
        
        #get supervisor by its id
        Route::get('', [SupervisorsController::class, 'index'])->name('supervisors.getAll');

        #get supervisor by its id
        Route::get('/{id}', [SupervisorsController::class, 'show'])->name('supervisors.getById');
        
        #create new supervisor
        Route::post('', [SupervisorsController::class, 'store'])->name('supervisors.create');
        
        
        #activate account
        Route::post('/{id}/activate', [SupervisorsController::class, 'activate'])->name('supervisors.activate');
        
        #suspend account
        Route::post('/{id}/suspend', [SupervisorsController::class, 'suspend'])->name('supervisors.suspend');
        
        #reset password
        Route::post('/{id}/reset-password', [SupervisorsController::class, 'resetPassword'])->name('supervisors.reset_password');

        #update account
        Route::put('/{id}', [SupervisorsController::class, 'update'])->name('supervisors.update');
        
        #remove account
        Route::delete('/{id}', [SupervisorsController::class, 'destroy'])->name('supervisors.remove');

        #get roles
        Route::get('/{id}/roles', [SupervisorsController::class, 'roles'])->name('supervisors.getRoles');

        #set role
        Route::post('/{id}/roles', [SupervisorsController::class, 'setRole'])->name('supervisors.setRole');

        #set role
        Route::delete('/{id}/roles', [SupervisorsController::class, 'revokeRoles'])->name('supervisors.revokeRoles');

        #get permissions
        Route::get('/{id}/permissions', [SupervisorsController::class, 'getPermissions'])->name('supervisors.getPermissions');


        
        // get from file
        // require __DIR__. DIRECTORY_SEPARATOR . 'api' . DIRECTORY_SEPARATOR . 'auth.php';
    });
});