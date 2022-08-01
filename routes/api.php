<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


require __DIR__ . DIRECTORY_SEPARATOR . 'supervisors' . DIRECTORY_SEPARATOR . 'api.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'roles' . DIRECTORY_SEPARATOR . 'api.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'api.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'locations' . DIRECTORY_SEPARATOR . 'api.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'messages' . DIRECTORY_SEPARATOR . 'api.php';
require __DIR__ . DIRECTORY_SEPARATOR . 'bookings' . DIRECTORY_SEPARATOR . 'api.php';

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Broadcast::routes(['middleware' => ['api', 'auth:sanctum']]);

