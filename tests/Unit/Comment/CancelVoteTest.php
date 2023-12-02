<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CancelVoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_cancel_vote_successfully(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();
        $user->commentVotes()->attach($comment, ['direction' => 1]);

        $response = $this->actingAs($user)->postJson("/api/comment/{$comment->id}/vote/cancel");

        $response->assertStatus(204);
    }

    public function test_cancel_vote_fails_for_unauthenticated_user(): void
    {
        $comment = Comment::factory()->create();

        $response = $this->postJson("/api/comment/{$comment->id}/vote/cancel");

        $response->assertStatus(401);
    }

    public function test_cancel_vote_fails_for_nonexistent_comment(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/comment/1/vote/cancel');

        $response->assertStatus(404);
    }

    public function test_cancel_vote_fails_for_non_voted_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/comment/{$comment->id}/vote/cancel");

        $response->assertStatus(409);
    }

    public function test_cancel_vote_for_downvoted_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create();
        $user->commentVotes()->attach($comment, ['direction' => -1]);

        $response = $this->actingAs($user)->postJson("/api/comment/{$comment->id}/vote/cancel");

        $response->assertStatus(204);
    }
}
