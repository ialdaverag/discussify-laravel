<?php

namespace App\Policies;

use App\Models\Community;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CommunityPolicy
{
    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Community $community): Response
    {
        if ($user->isOwnerOf($community)) {
            return Response::allow();
        }
    
        return Response::deny('You are not the owner of this community.');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Community $community): Response
    {
        if ($user->isOwnerOf($community) || $user->isModeratorOf($community)) {
            return Response::allow();
        }
    
        return Response::deny('You are not the owner of this community.');
    }

    /**
     * Determine whether the user can add a moderator to the community.
     */
    public function addModerator(User $user, Community $community): Response
    {
        if ($user->isOwnerOf($community)) {
            return Response::allow();
        }
    
        return Response::deny('You are not the owner of this community.');
    }

    /**
     * Determine whether the user can remove a moderator from the community.
     */
    public function removeModerator(User $user, Community $community): Response
    {
        if ($user->isOwnerOf($community)) {
            return Response::allow();
        }
    
        return Response::deny('You are not the owner of this community.');
    }

    /**
     * Determine whether the user can ban a user from the community.
     */
    public function banUser(User $user, Community $community): Response
    {
        if ($user->isModeratorOf($community)) {
            return Response::allow();
        }
    
        return Response::deny('You are not a moderator of this community.');
    }

    /**
     * Determine whether the user can unban a user from the community.
     */
    public function unbanUser(User $user, Community $community): Response
    {
        if ($user->isModeratorOf($community)) {
            return Response::allow();
        }
    
        return Response::deny('You are not a moderator of this community.');
    }
}
