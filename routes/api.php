<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;

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
    Route::post('/{name}/unmod/{username}', [CommunityController::class, 'removeModerator']);
    Route::post('/{name}/ban/{username}', [CommunityController::class, 'banUser']);
    Route::post('/{name}/unban/{username}', [CommunityController::class, 'unbanUser']);

    Route::get('/{community:name}/subscribers', [CommunityController::class, 'getSubscribers']);
    Route::get('/{community:name}/moderators', [CommunityController::class, 'getModerators']);
    Route::get('/{community:name}/banned', [CommunityController::class, 'getBans']);
});

Route::group(['middleware' => 'api', 'prefix' => 'post'], function ($router) {
    Route::post('/', [PostController::class, 'store']);
    Route::get('/', [PostController::class, 'index']);
    Route::get('/{post:id}', [PostController::class, 'show']);
    Route::patch('/{post:id}', [PostController::class, 'update']);
    Route::delete('/{post:id}', [PostController::class, 'destroy']);

    Route::post('/{post:id}/bookmark', [PostController::class, 'bookmark']);
    Route::post('/{post:id}/unbookmark', [PostController::class, 'unbookmark']);

    Route::post('/{post:id}/vote/up', [PostController::class, 'upvote']);
    Route::post('/{post:id}/vote/down', [PostController::class, 'downvote']);
    Route::post('/{post:id}/vote/cancel', [PostController::class, 'unvote']);
});

Route::group(['middleware' => 'api', 'prefix' => 'comment'], function ($router) {
    Route::post('/', [CommentController::class, 'store']);
    // Route::get('/', [CommentController::class, 'index']);
    // Route::get('/{comment:id}', [CommentController::class, 'show']);
    // Route::patch('/{comment:id}', [CommentController::class, 'update']);
    // Route::delete('/{comment:id}', [CommentController::class, 'destroy']);
});