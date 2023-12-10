<?php

namespace Tests\Unit\Post;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyTest extends TestCase
{
    private $route = 'api/post/%s';

    use RefreshDatabase;

    public function test_destroy_as_owner_successfully(): void
    {
        // Create a user
        $owner = User::factory()->create();

        // Create a post
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // Send a DELETE request to /api/post/{id}
        $response = $this->actingAs($owner)->deleteJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }

    public function test_destroy_as_moderator_successfully(): void
    {
        // Create an admin
        $moderator = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Extract the community from the post
        $community = $post->community;

        // Make the user a moderator of the community
        $community->moderators()->attach($moderator->id);

        // Send a DELETE request to /api/post/{id}
        $response = $this->actingAs($moderator)->deleteJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }

    public function test_destroy_fails_for_non_owner(): void
    {
        // Create a user
        $owner = User::factory()->create();

        // Create a post
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // Create another user
        $nonOwner = User::factory()->create();

        // Send a DELETE request to /api/post/{id}
        $response = $this->actingAs($nonOwner)->deleteJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 403
        $response->assertStatus(403);
    }

    public function test_destroy_fails_for_guest(): void
    {
        // Create a post
        $owner = User::factory()->create();

        // Create a post
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // Send a DELETE request to /api/post/{id}
        $response = $this->deleteJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 401
        $response->assertStatus(401);
    }

    public function test_destroy_fails_for_post_not_found(): void
    {
        // Create a user
        $owner = User::factory()->create();

        // Send a DELETE request to /api/post/{id}
        $response = $this->actingAs($owner)->deleteJson(sprintf($this->route, 1));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
