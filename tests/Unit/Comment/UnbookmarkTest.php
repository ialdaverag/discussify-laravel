<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnbookmarkTest extends TestCase
{
    private $route = '/api/comment/%s/unbookmark';

    use RefreshDatabase;

    public function test_unbookmark_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a comment
        $comment = Comment::factory()->create();

        // Attach the comment to the user's bookmarks
        $user->commentBookmarks()->attach($comment);

        // Send a request to the API as the user
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }

    public function test_unbookmark_fails_for_unauthenticated_user(): void
    {
        // Create a comment
        $comment = Comment::factory()->create();

        // Send a request to the API as the user
        $response = $this->postJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 401
        $response->assertStatus(401);
    }

    public function test_unbookmark_fails_for_nonexistent_comment(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Send a request to the API as the user
        $response = $this->actingAs($user)->postJson(sprintf($this->route, 1));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }

    public function test_not_bookmarked(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a comment
        $comment = Comment::factory()->create();

        // Send a request to the API as the user
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 409
        $response->assertStatus(409);
    }
}
