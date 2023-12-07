<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;
use App\Models\Post;

class Community extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'about',
    ];

    /**
     * Get the user that owns the community.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOwnerBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    /**
     * Get the subscribers of the community.
     */
    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'subscribers');
    }

    /**
     * Get the moderators of the community.
     */
    public function moderators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'moderators', 'community_id', 'user_id');
    }

    /**
     * Get the baneed users of the community.
     */
    public function bans(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'bans', 'community_id', 'user_id');
    }

    /**
     * Get the posts of the community.
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }
}
