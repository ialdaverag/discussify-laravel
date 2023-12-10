<?php

namespace Tests\Unit\Post;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CancelVotePost extends TestCase
{
    private $route = '/api/post/%s/vote/cancel';

    use RefreshDatabase;

    public function test_cancel_vote_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Attach the post to the user's votes
        $user->votes()->attach($post, ['direction' => 1]);

        // Send a POST request to /api/post/{id}/vote/cancel
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }

    public function test_cancel_vote_being_banned(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Attach the post to the user's votes
        $user->votes()->attach($post, ['direction' => 1]);

        // Ban the user
        $post->community->bans()->attach($user->id);

        // Send a POST request to /api/post/{id}/vote/cancel
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 403
        $response->assertStatus(403);
    }

    public function test_cancel_vote_fails_for_guest(): void
    {
        // Create a post
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Attach the post to the user's votes
        $user->votes()->attach($post, ['direction' => 1]);

        // Send a POST request to /api/post/{id}/vote/cancel
        $response = $this->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 401
        $response->assertStatus(401);
    }

    public function test_cancel_vote_fails_for_post_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(sprintf($this->route, 1));

        $response->assertStatus(404);
    }

    public function test_not_voted(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson(sprintf($this->route, $post->id));

        $response->assertStatus(400);
    }
}
