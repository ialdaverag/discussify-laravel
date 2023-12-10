<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;
use App\Models\Community;

use Illuminate\Auth\Access\Response;

class PostPolicy
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
    public function update(User $user, Post $post): Response
    {
        if ($user->id !== $post->user_id) {
            return Response::deny('You are not the owner of this post.');
        }
    
        return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Post $post): Response
    {
        if ($user->id === $post->user_id || $user->isModeratorOf($post->community)) {
            return Response::allow();
        }
    
        if ($user->id !== $post->user_id) {
            return Response::deny('You are not the owner of this post.');
        }
    
        if (!$user->isModeratorOf($post->community)) {
            return Response::deny('You are not a moderator of this community.');
        }
    }

    /**
     * Determine whether the user can upvote the model.
     */
    public function vote(User $user, Post $post): Response
    {
        if ($user->isBannedFrom($post->community)) {
            return Response::deny('You are banned from this community.');
        }
    
        return Response::allow();
    }
}
