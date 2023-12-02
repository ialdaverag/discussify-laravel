<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_bookmark_successfully(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson("/api/comment/{$comment->id}/bookmark");

        $response->assertStatus(204);
    }

    public function test_bookmark_fails_for_unauthenticated_user(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->postJson("/api/comment/{$comment->id}/bookmark");

        $response->assertStatus(401);
    }

    public function test_bookmark_fails_for_nonexistent_comment(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/comment/1/bookmark');

        $response->assertStatus(404);
    }

    public function test_already_bookmarked(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);
        $user->commentBookmarks()->attach($comment);

        $response = $this->actingAs($user)->postJson("/api/comment/{$comment->id}/bookmark");

        $response->assertStatus(409);
    }
}
