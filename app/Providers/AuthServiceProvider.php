<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use App\Models\Comment;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     * 
     * @return void
     */
    public function boot(): void
    {
        Gate::define('update-community', function (User $user, Community $community) {
            return $user->id === $community->user_id;
        });

        Gate::define('delete-community', function (User $user, Community $community) {
            return $user->id === $community->user_id;
        });

        Gate::define('add-moderator', function (User $user, Community $community) {
            return $user->id === $community->user_id;
        });

        Gate::define('remove-moderator', function (User $user, Community $community) {
            return $user->id === $community->user_id;
        });

        Gate::define('ban-user', function (User $user, Community $community) {
            return $user->isModeratorOf($community);
        });

        Gate::define('unban-user', function (User $user, Community $community) {
            return $user->isModeratorOf($community);
        });

        Gate::define('update-post', function (User $user, Post $post) {
            return $user->id === $post->user_id;
        });

        Gate::define('delete-post', function (User $user, Post $post) {
            return $user->id === $post->user_id;
        });

        Gate::define('update-comment', function (User $user, Comment $comment) {
            return $user->id === $comment->user_id;
        });

        Gate::define('delete-comment', function (User $user, Comment $comment) {
            return $user->id === $comment->user_id;
        });
    }
}
