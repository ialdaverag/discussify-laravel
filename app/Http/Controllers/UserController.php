<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }

    public function index()
    {
        return User::all();
    }

    public function show(User $user)
    {
        return $user;
    }

    public function follow(User $user)
    {
        if ($user->id === auth()->user()->id) {
            return response()->json([
                'message' => 'You cannot follow yourself.'
            ], 422);
        }

        $user->followers()->attach(auth()->user()->id);

        return response()->json([
            'message' => 'Successfully followed user.'
        ], 204);
    }

    public function unfollow(User $user)
    {
        if ($user->id === auth()->user()->id) {
            return response()->json([
                'message' => 'You cannot unfollow yourself.'
            ], 422);
        }

        $user->followers()->detach(auth()->user()->id);

        return response()->json([
            'message' => 'Successfully unfollowed user.'
        ], 204);
    }

    public function getFollowers(User $user)
    {
        return $user->followers;
    }

    public function getFollowing(User $user)
    {
        return $user->following;
    }

    public function getSubscriptions(User $user)
    {
        return $user->subscriptions;
    }

    public function getPosts(User $user)
    {
        return $user->posts;
    }
}
