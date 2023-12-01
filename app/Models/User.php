<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

use App\Models\Community;
use App\Models\Post;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
    ];

    /**
     * Get the communities that the user owns.
     */
    public function communities(): HasMany
    {
        return $this->hasMany(Community::class);
    }

    /**
     * Get the subscriptions of the user.
     */
    public function subscriptions(): BelongsToMany
    {
        return $this->belongsToMany(Community::class, 'subscribers');
    }

    /**
     * Get the moderations of the user.
     */
    public function moderations(): BelongsToMany
    {
        return $this->belongsToMany(Community::class, 'moderators');
    }

    /**
     * Get the bans of the user.
     */
    public function bans(): BelongsToMany
    {
        return $this->belongsToMany(Community::class, 'bans');
    }

    /**
     * Get the posts of the user.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Get the bookmarks of the user.
     */
    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_bookmarks');
    }

    /**
     * Get the votes of the user.
     */
    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_votes')->withPivot('direction');
    }

    /**
     * Check if the user is subscribed to a community.
     */
    public function isSubscribedTo(Community $community)
    {
        return $this->subscriptions()->where('community_id', $community->id)->exists();
    }

    /**
     * Check if the user is a moderator of a community.
     */
    public function isModeratorOf(Community $community)
    {
        return $this->moderations()->where('community_id', $community->id)->exists();
    }

    /**
     * Check if the user is banned from a community.
     */
    public function isBannedFrom(Community $community)
    {
        return $this->bans()->where('community_id', $community->id)->exists();
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
