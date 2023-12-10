<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\User;
use App\Models\Community;
use App\Models\Comment;

class Post extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'content',
        'community_id'
    ];

    /**
     * Get the user that owns the post.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the community that owns the post.
     */
    public function community(): BelongsTo
    {
        return $this->belongsTo(Community::class);
    }

    /**
     * Get the bookmarks for the post.
     */
    public function bookmarks(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_bookmarks', 'post_id', 'user_id');
    }

    /**
     * Get the votes for the post.
     */
    public function votes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_votes')->withPivot('direction');
    }

    /**
     * Get the comments for the post.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * Check if the post is bookmarked by the given user.
     *
     * @var array<int, string>
     */
    public function isBookmarkedBy(User $user): bool
    {
        return $this->bookmarks()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if the post is voted on by the given user.
     *
     * @var array<int, string>
     */
    public function isVotedOnBy(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->exists();
    }

    /**
     * Check if the post is upvoted by the given user.
     *
     * @var array<int, string>
     */
    public function isUpvotedBy(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->wherePivot('direction', 1)->exists();
    }

    /**
     * Check if the post is downvoted by the given user.
     *
     * @var array<int, string>
     */
    public function isDownvotedBy(User $user): bool
    {
        return $this->votes()->where('user_id', $user->id)->wherePivot('direction', -1)->exists();
    }
}
