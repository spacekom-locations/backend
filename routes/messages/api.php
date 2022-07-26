<?php

use App\Http\Controllers\Messages\ThreadsController;
use Illuminate\Support\Facades\Route;

Route::prefix('messages')->middleware('auth:sanctum', 'auth:users')->group(function () {
    Route::get('/', [ThreadsController::class, 'index'])->name('messages.threads.index');
    Route::post('/', [ThreadsController::class, 'store'])->name('messages.threads.store');
    Route::get('/{id}', [ThreadsController::class, 'show'])->name('messages.thread.show');
    Route::post('/{id}', [ThreadsController::class, 'sendMessage'])->name('messages.thread.sendMessage');
});
