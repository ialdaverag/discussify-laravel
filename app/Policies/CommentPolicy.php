<?php

namespace App\Policies;

use App\Models\Comment;
use App\Models\Community;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommentPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Community $community): Response
    {
        if (!$user->isSubscribedTo($community)) {
            return Response::deny('You are not subscribed to this community.');
        }
    
        if ($user->isBannedFrom($community)) {
            return Response::deny('You are banned from this community.');
        }
    
        return Response::allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Comment $comment): Response
    {
        if ($user->id !== $comment->user_id) {
            return Response::deny('You are not the owner of this comment.');
        }
    
        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Comment $comment): Response
    {
        if ($user->id === $comment->user_id || $user->isModeratorOf($comment->post->community)) {
            return Response::allow();
        }
    
        if ($user->id !== $comment->user_id) {
            return Response::deny('You are not the owner of this comment.');
        }
    
        if (!$user->isModeratorOf($comment->post->community)) {
            return Response::deny('You are not a moderator of this community.');
        }
    }
}
