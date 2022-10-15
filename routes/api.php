<?php

use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\UserController;
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

Route::post('/auth/register', [ApiAuthController::class, 'registerUser']);
Route::post('/auth/login', [ApiAuthController::class, 'loginUser']);
Route::post('/auth/logout', [ApiAuthController::class, 'logoutUser']);

Route::apiResource('/users', UserController::class)->middleware('auth:sanctum');
