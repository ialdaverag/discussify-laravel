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

    public function getComments(User $user)
    {
        return $user->comments;
    }

    public function getBookmarkedPosts()
    {
        $user = auth()->user();

        $bookmarkedPosts = $user->bookmarks()->get();

        return response()->json($bookmarkedPosts, 200);
    }
        
    public function getUpvotedPosts()
    {
        $user = auth()->user();

        $upvotedPosts = $user->votes()->wherePivot('direction', 1)->get();

        return response()->json($upvotedPosts, 200);
    }

    public function getDownvotedPosts()
    {
        $user = auth()->user();

        $downvotedPosts = $user->votes()->wherePivot('direction', -1)->get();

        return response()->json($downvotedPosts, 200);
    }

    public function getBookmarkedComments()
    {
        $user = auth()->user();

        $bookmarkedComments = $user->commentBookmarks()->get();

        return response()->json($bookmarkedComments, 200);
    }

    public function getUpvotedComments()
    {
        $user = auth()->user();

        $upvotedComments = $user->commentVotes()->wherePivot('direction', 1)->get();

        return response()->json($upvotedComments, 200);
    }

    public function getDownvotedComments()
    {
        $user = auth()->user();

        $downvotedComments = $user->commentVotes()->wherePivot('direction', -1)->get();

        return response()->json($downvotedComments, 200);
    }
}