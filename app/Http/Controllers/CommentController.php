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
}
