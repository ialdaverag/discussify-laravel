<?php

namespace Tests\Unit\Post;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DownvoteTest extends TestCase
{
    private $route = '/api/post/%s/vote/down';

    use RefreshDatabase;

    public function test_downvote_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Send a POST request to /api/post/{id}/vote/down
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }

    public function test_upvote_being_banned(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Ban the user
        $post->community->bans()->attach($user->id);

        // Send a POST request to /api/post/{id}/vote/down
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 403
        $response->assertStatus(403);
    }

    public function test_downvote_fails_for_guest(): void
    {
        // Create a post
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Send a POST request to /api/post/{id}/vote/down
        $response = $this->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 401
        $response->assertStatus(401);
    }

    public function test_downvote_fails_for_post_not_found(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Send a POST request to /api/post/{id}/vote/down
        $response = $this->actingAs($user)->postJson(sprintf($this->route, 1));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }

    public function test_already_downvoted(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Attach the post to the user's downvotes
        $user->votes()->attach($post, ['direction' => -1]);

        // Send a POST request to /api/post/{id}/vote/down
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 409
        $response->assertStatus(400);
    }

    public function test_upvoted(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Attach the post to the user's upvotes
        $user->votes()->attach($post, ['direction' => 1]);

        // Send a POST request to /api/post/{id}/vote/down
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 409
        $response->assertStatus(200);
    }
}
