<?php

use App\Http\Controllers\Supervisors\RolesController;
use Illuminate\Support\Facades\Route;

Route::prefix('roles')->middleware('auth:sanctum', 'auth:supervisors')->group(function () {

    #create new role
    Route::post('/', [RolesController::class, 'create'])->name('roles.create');

    
    #get all roles
    Route::get('/', [RolesController::class, 'index'])->name('roles.index');

    #get one role
    Route::get('/{id}', [RolesController::class, 'show'])->name('roles.show');

    #update role
    Route::put('/{id}', [RolesController::class, 'update'])->name('roles.update');

    #remove role
    Route::delete('/{id}', [RolesController::class, 'destroy'])->name('roles.remove');



    #get role permissions
    Route::get('/{id}/permissions', [RolesController::class, 'getRolePermissions'])->name('roles.getRolePermissions');

});