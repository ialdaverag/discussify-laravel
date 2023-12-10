<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Models\Post;
use App\Models\Community;
use App\Http\Requests\Post\StoreRequest;
use App\Http\Requests\Post\UpdateRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostCollection;
use App\Http\Resources\UserCollection;
use App\Http\Resources\CommentCollection;

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
        return response()->json(new PostCollection(Post::all()));

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRequest $request)
    {
        $community = Community::findOrFail($request->community_id);
        
        $response = Gate::inspect('create', [Post::class, $community]);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], 403);
        }

        $post = auth()->user()->posts()->create($request->validated());

        return response()->json(new PostResource($post), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return response()->json(new PostResource($post), 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRequest $request, Post $post)
    {
        $response = Gate::inspect('update', $post);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], 403);
        }

        $post->update($request->validated());

        return response()->json(new PostResource($post), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        $response = Gate::inspect('delete', $post);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], 403);
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

        if ($post->isBookmarkedBy($user)) {
            return response()->json(['error' => 'Post already bookmarked'], 409);
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

        if (!$post->isBookmarkedBy($user)) {
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
        $response = Gate::inspect('vote', $post);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], 403);
        }

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
        $response = Gate::inspect('vote', $post);

        if ($response->denied()) {
            return response()->json(['error' => $response->message()], 403);
        }

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

    /**
     * Remove the specified resource from storage.
     */
    public function unvote(Post $post)
    {
        $user = auth()->user();

        if (!$user->votes()->where('post_id', $post->id)->exists()) {
            return response()->json(['error' => 'Post not voted'], 400);
        }

        $user->votes()->detach($post);

        return response()->json(null, 204);
    }

    /**
     * Get upvoters of the specified resource from storage.
     */
    public function getUpvoters(Post $post)
    {
        $upvoters = $post->votes()->wherePivot('direction', 1)->get();

        return response()->json(new UserCollection($upvoters));
    }

    /**
     * Get downvoters of the specified resource from storage.
     */
    public function getDownvoters(Post $post)
    {
        $downvoters = $post->votes()->wherePivot('direction', -1)->get();

        return response()->json(new UserCollection($downvoters));
    }

    /**
     * Get comments of the specified resource from storage.
     */
    public function getComments(Post $post)
    {
        $comments = $post->comments;

        return response()->json(new CommentCollection($comments));
    }
}