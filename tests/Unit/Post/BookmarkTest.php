<?php

namespace Tests\Unit\Post;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookmarkTest extends TestCase
{
    private $route = '/api/post/%s/bookmark';

    use RefreshDatabase;

    public function test_bookmark_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Send a POST request to /api/post/{id}/bookmark
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }

    public function test_bookmark_fails_for_guest(): void
    {
        // Create a post
        $post = Post::factory()->create();

        // Send a POST request to /api/post/{id}/bookmark
        $response = $this->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 401
        $response->assertStatus(401);
    }

    public function test_bookmark_fails_for_post_not_found(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Send a POST request to /api/post/{id}/bookmark
        $response = $this->actingAs($user)->postJson(sprintf($this->route, 1));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }

    public function test_already_bookmarked(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Attach the post to the user's bookmarks
        $user->bookmarks()->attach($post);

        // Send a POST request to /api/post/{id}/bookmark
        $response = $this->actingAs($user)->postJson("/api/post/{$post->id}/bookmark");

        // Assert that the response has status code 409
        $response->assertStatus(409);
    }
}
