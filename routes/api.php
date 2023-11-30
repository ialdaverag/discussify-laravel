<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommunityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::group(['middleware' => 'api', 'prefix' => 'auth'], function ($router) {
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::group(['middleware' => 'api', 'prefix' => 'user'], function ($router) {
    Route::get('/', [UserController::class, 'index']);
    Route::get('/{user:username}', [UserController::class, 'show']);
});

Route::group(['middleware' => 'api', 'prefix' => 'community'], function ($router) {
    Route::post('/', [CommunityController::class, 'store']);
    Route::get('/', [CommunityController::class, 'index']);
    Route::get('/{community:name}', [CommunityController::class, 'show']);
    Route::patch('/{community:name}', [CommunityController::class, 'update']);
    Route::delete('/{community:name}', [CommunityController::class, 'destroy']);

    Route::post('/{community:name}/subscribe', [CommunityController::class, 'subscribe']);
    Route::post('/{community:name}/unsubscribe', [CommunityController::class, 'unsubscribe']);
    Route::post('/{name}/mod/{username}', [CommunityController::class, 'addModerator']);
    // Route::post('/{community:name}/unmod/{user:username}', [CommunityController::class, 'removeModerator']);
    // Route::post('/{community:name}/ban/{user:username}', [CommunityController::class, 'banUser']);
    // Route::post('/{community:name}/unban/{user:username}', [CommunityController::class, 'unbanUser']);
});