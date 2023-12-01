<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Post;

use App\Http\Requests\PostStoreRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Http\Resources\PostResource;

class PostController extends Controller
{
    /**
     * Create a new CommunityController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }
    
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PostResource::collection(Post::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostStoreRequest $request)
    {
        $post = auth()->user()->posts()->create($request->validated());

        return response()->json(new PostResource($post), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return new PostResource($post);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        if (Gate::denies('update-post', $post)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->update($request->validated());

        return response()->json(new PostResource($post), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if (Gate::denies('delete-post', $post)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $post->delete();

        return response()->json(null, 204);
    }

    /**
     * Bookmark the specified resource from storage.
     */ 
    public function bookmark(Post $post)
    {
        $user = auth()->user();

        if ($user->bookmarks()->where('post_id', $post->id)->exists()) {
            return response()->json(['error' => 'Post already bookmarked'], 400);
        }

        $user->bookmarks()->attach($post);

        return response()->json(null, 204);
    }

    /**
     * Unbookmark the specified resource from storage.
     */

    public function unbookmark(Post $post)
    {
        $user = auth()->user();

        if (!$user->bookmarks()->where('post_id', $post->id)->exists()) {
            return response()->json(['error' => 'Post not bookmarked'], 400);
        }

        $user->bookmarks()->detach($post);

        return response()->json(null, 204);
    }

    /**
     * Upvote the specified resource from storage.
     */
    public function upvote(Post $post)
    {
        $user = auth()->user();

        if ($user->votes()->where('post_id', $post->id)->exists()) {
            $vote = $user->votes()->where('post_id', $post->id)->first();

            if ($vote->pivot->direction == -1) {
                $vote->pivot->direction = 1;
                $vote->pivot->save();

                return response()->json(['message' => 'Vote changed to positive'], 200);
            } else {
                return response()->json(['error' => 'Post already voted'], 400);
            }
        } else {
            $user->votes()->attach($post, ['direction' => 1]);

            return response()->json(['message' => 'Post upvoted successfully'], 204);
        }
    }

    /**
     * Downvote the specified resource from storage.
     */
    public function downvote(Post $post)
    {
        $user = auth()->user();

        if ($user->votes()->where('post_id', $post->id)->exists()) {
            $vote = $user->votes()->where('post_id', $post->id)->first();

            if ($vote->pivot->direction == 1) {
                $vote->pivot->direction = -1;
                $vote->pivot->save();

                return response()->json(['message' => 'Vote changed to negative'], 200);
            } else {
                return response()->json(['error' => 'Post already voted'], 400);
            }
        } else {
            $user->votes()->attach($post, ['direction' => -1]);

            return response()->json(['message' => 'Post downvoted successfully'], 204);
        }
    }
}