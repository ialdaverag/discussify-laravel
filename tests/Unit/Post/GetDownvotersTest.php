<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetDownvotersTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_downvoters_successfully()
    {
        // Create a user and a post
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Make the user downvote the post
        $user->votes()->attach($post, ['direction' => -1]);

        // Send a GET request to /api/post/{id}/downvoters
        $response = $this->actingAs($user)->getJson("/api/post/{$post->id}/downvoters");

        // Assert that the response has status code 200
        $response->assertStatus(200);

        // Assert that the user's id is present in the response
        $response->assertJsonFragment(['id' => $user->id]);
    }

    public function test_get_downvoters_for_non_existing_post()
    {
        // Create a user
        $user = User::factory()->create();

        // ID of a post that doesn't exist
        $nonExistingPostId = 123456; 

        // Send a GET request to /api/post/{id}/downvoters
        $response = $this->actingAs($user)->getJson("/api/post/{$nonExistingPostId}/downvoters");

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
