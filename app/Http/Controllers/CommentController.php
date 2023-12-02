<?php

namespace App\Http\Controllers;

use App\Models\Comment;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

use App\Http\Requests\CommentStoreRequest;
use App\Http\Requests\CommentUpdateRequest;
use App\Http\Resources\CommentResource;

class CommentController extends Controller
{
    /**
     * Create a new CommentController instance.
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
        return CommentResource::collection(Comment::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentStoreRequest $request)
    {
        $validated = $request->validated();

        $comment = new Comment($validated);
        $comment->user_id = auth()->id();
        $comment->post_id = $validated['post_id'];

        // If the request includes a 'comment_id', this comment is a reply to another comment.
        if (isset($validated['comment_id'])) {
            $comment->comment_id = $validated['comment_id'];
        }

        $comment->save();

        return response()->json($comment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        return new CommentResource($comment);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentUpdateRequest $request, Comment $comment)
    {
        if (Gate::denies('update-comment', $comment)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validated();

        $comment->update($validated);

        return response()->json($comment, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        if (Gate::denies('delete-comment', $comment)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $comment->delete();

        return response()->json(null, 204);
    }

    /**
     * Bookmark the specified resource from storage.
     */
    public function bookmark(Comment $comment)
    {
        $user = auth()->user();

        if ($user->commentBookmarks()->where('comment_bookmarks.comment_id', $comment->id)->exists()) {
            return response()->json(['error' => 'Comment already bookmarked'], 409);
        }

        $user->commentBookmarks()->attach($comment);

        return response()->json(null, 204);
    }

    /**
     * Unbookmark the specified resource from storage.
     */
    public function unbookmark(Comment $comment)
    {
        $user = auth()->user();

        if (!$user->commentBookmarks()->where('comment_bookmarks.comment_id', $comment->id)->exists()) {
            return response()->json(['error' => 'Comment is not bookmarked'], 409);
        }

        $user->commentBookmarks()->detach($comment);

        return response()->json(null, 204);
    }

    /**
     * Upvote the specified resource from storage.
     */
    public function upvote(Comment $comment)
    {
        $user = auth()->user();

        $existingVote = $user->commentVotes()->where('comment_votes.comment_id', $comment->id)->first();

        if ($existingVote) {
            if ($existingVote->pivot->direction === 1) {
                return response()->json(['error' => 'Comment already upvoted'], 409);
            } else {
                $existingVote->pivot->direction = 1;
                $existingVote->pivot->save();

                return response()->json(null, 204);
            }
        }

        $user->commentVotes()->attach($comment, ['direction' => 1]);

        return response()->json(null, 204);
    }

    /**
     * Downvote the specified resource from storage.
     */
    public function downvote(Comment $comment)
    {
        $user = auth()->user();

        $existingVote = $user->commentVotes()->where('comment_votes.comment_id', $comment->id)->first();

        if ($existingVote) {
            if ($existingVote->pivot->direction === -1) {
                return response()->json(['error' => 'Comment already downvoted'], 409);
            } else {
                $existingVote->pivot->direction = -1;
                $existingVote->pivot->save();

                return response()->json(null, 204);
            }
        }

        $user->commentVotes()->attach($comment, ['direction' => -1]);

        return response()->json(null, 204);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function unvote(Comment $comment)
    {
        $user = auth()->user();

        $existingVote = $user->commentVotes()->where('comment_votes.comment_id', $comment->id)->first();

        if (!$existingVote) {
            return response()->json(['error' => 'Comment is not voted'], 409);
        }

        $user->commentVotes()->detach($comment);

        return response()->json(null, 204);
    }

    /**
     * Get the upvoters of the specified resource from storage.
     */
    public function getUpvoters(Comment $comment)
    {
        $upvoters = $comment->votes()->wherePivot('direction', 1)->get();

        return response()->json($upvoters, 200);
    }

    /**
     * Get the downvoters of the specified resource from storage.
     */
    public function getDownvoters(Comment $comment)
    {
        $downvoters = $comment->votes()->wherePivot('direction', -1)->get();

        return response()->json($downvoters, 200);
    }
}
