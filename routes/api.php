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

    Route::post('/{user:username}/follow', [UserController::class, 'follow']);
    Route::post('/{user:username}/unfollow', [UserController::class, 'unfollow']);

    Route::get('/{user:username}/followers', [UserController::class, 'getFollowers']);
    Route::get('/{user:username}/following', [UserController::class, 'getFollowing']);
    Route::get('/{user:username}/subscriptions', [UserController::class, 'getSubscriptions']);
    Route::get('/{user:username}/posts', [UserController::class, 'getPosts']);
    Route::get('/{user:username}/comments', [UserController::class, 'getComments']);

    Route::get('/posts/upvoted ', [UserController::class, 'getUpvotedPosts']);
    Route::get('/posts/downvoted ', [UserController::class, 'getDownvotedPosts']);

    Route::get('/comments/upvoted ', [UserController::class, 'getUpvotedComments']);
    // Route::get('/comments/downvoted ', [UserController::class, 'getDownvotedComments']);
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

    Route::get('/{community:name}/posts', [CommunityController::class, 'getPosts']);
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

    Route::get('/{post:id}/upvoters', [PostController::class, 'getUpvoters']);
    Route::get('/{post:id}/downvoters', [PostController::class, 'getDownvoters']);

    Route::get('/{post:id}/comments', [PostController::class, 'getComments']);
});

Route::group(['middleware' => 'api', 'prefix' => 'comment'], function ($router) {
    Route::post('/', [CommentController::class, 'store']);
    Route::get('/', [CommentController::class, 'index']);
    Route::get('/{comment:id}', [CommentController::class, 'show']);
    Route::patch('/{comment:id}', [CommentController::class, 'update']);
    Route::delete('/{comment:id}', [CommentController::class, 'destroy']);

    Route::post('/{comment:id}/bookmark', [CommentController::class, 'bookmark']);
    Route::post('/{comment:id}/unbookmark', [CommentController::class, 'unbookmark']);

    Route::post('/{comment:id}/vote/up', [CommentController::class, 'upvote']);
    Route::post('/{comment:id}/vote/down', [CommentController::class, 'downvote']);
    Route::post('/{comment:id}/vote/cancel', [CommentController::class, 'unvote']);

    Route::get('/{comment:id}/upvoters', [CommentController::class, 'getUpvoters']);
    Route::get('/{comment:id}/downvoters', [CommentController::class, 'getDownvoters']);
});