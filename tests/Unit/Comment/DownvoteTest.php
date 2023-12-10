<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DownvoteTest extends TestCase
{
    private $route = '/api/comment/%s/vote/down';

    use RefreshDatabase;

    public function test_downvote_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a comment
        $comment = Comment::factory()->create();

        // Send a POST request to the API as the user
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 201
        $response->assertStatus(204);
    }

    public function test_downvote_fails_for_unauthenticated_user(): void
    {
        // Create a comment
        $comment = Comment::factory()->create();

        // Send a POST request to the API as the user
        $response = $this->postJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 401
        $response->assertStatus(401);
    }

    public function test_downvote_fails_for_nonexistent_comment(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Send a POST request to the API as the user
        $response = $this->actingAs($user)->postJson(sprintf($this->route, 1));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }

    public function test_downvote_fails_for_already_downvoted_comment(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a comment
        $comment = Comment::factory()->create();

        // Attach the user to the comment's downvoters
        $user->commentVotes()->attach($comment, ['direction' => -1]);

        // Send a POST request to the API as the user
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 409
        $response->assertStatus(409);
    }

    public function test_downvote_for_upvoted_comment(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a comment
        $comment = Comment::factory()->create();

        // Attach the user to the comment's upvoters
        $user->commentVotes()->attach($comment, ['direction' => 1]);

        // Send a POST request to the API as the user
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }
}
