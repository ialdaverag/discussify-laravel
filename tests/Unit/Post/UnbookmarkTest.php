<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;

use Tests\TestCase;

use Illuminate\Foundation\Testing\RefreshDatabase;

class UnbookmarkTest extends TestCase
{
    private $route = '/api/post/%s/unbookmark';

    use RefreshDatabase;

    public function test_unbookmark_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // attach the post to the user's bookmarks
        $user->bookmarks()->attach($post);

        // Send a POST request to /api/post/{id}/unbookmark
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }

    public function test_unbookmark_fails_for_guest(): void
    {
        // Create a post
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // attach the post to the user's bookmarks
        $user->bookmarks()->attach($post);

        // Send a POST request to /api/post/{id}/unbookmark
        $response = $this->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 401
        $response->assertStatus(401);
    }

    public function test_unbookmark_fails_for_post_not_found(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Send a POST request to /api/post/{id}/unbookmark
        $response = $this->actingAs($user)->postJson(sprintf($this->route, 1));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }

    public function test_not_bookmarked(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create(['user_id' => $user->id]);

        // Send a POST request to /api/post/{id}/unbookmark
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 400
        $response->assertStatus(400);
    }
}
