<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DownvoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_downvote_successfully(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/comment/{$comment->id}/vote/down");

        $response->assertStatus(204);
    }

    public function test_downvote_fails_for_unauthenticated_user(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->postJson("/api/comment/{$comment->id}/vote/down");

        $response->assertStatus(401);
    }

    public function test_downvote_fails_for_nonexistent_comment(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/comment/1/vote/down');

        $response->assertStatus(404);
    }

    public function test_downvote_fails_for_already_downvoted_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();
        $user->commentVotes()->attach($comment, ['direction' => -1]);

        $response = $this->actingAs($user)->postJson("/api/comment/{$comment->id}/vote/down");

        $response->assertStatus(409);
    }

    public function test_downvote_for_upvoted_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();
        $user->commentVotes()->attach($comment, ['direction' => 1]);

        $response = $this->actingAs($user)->postJson("/api/comment/{$comment->id}/vote/down");

        $response->assertStatus(204);
    }
}
