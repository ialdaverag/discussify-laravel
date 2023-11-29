<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

use App\Models\User;
use App\Models\Community;

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

        Gate::define('subscribe-community', function (User $user, Community $community) {
            return !$community->subscribers()->where('user_id', $user->id)->exists();
        });
    }
}
