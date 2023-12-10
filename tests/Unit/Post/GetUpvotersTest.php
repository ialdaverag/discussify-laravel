<?php

namespace Tests\Unit\Post;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUpvotersTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_upvoters_successfully()
    {
        // Create a user and a post
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Make the user upvote the post
        $user->votes()->attach($post, ['direction' => 1]);

        // Send a GET request to /api/post/{id}/upvoters
        $response = $this->actingAs($user)->getJson("/api/post/{$post->id}/upvoters");

        // Assert that the response has status code 200
        $response->assertStatus(200);

        // Assert that the user's id is present in the response
        $response->assertJsonFragment(['id' => $user->id]);
    }

    public function test_get_upvoters_for_non_existing_post()
    {
        // Create a user
        $user = User::factory()->create();

        // ID of a post that doesn't exist
        $nonExistingPostId = 123456; 

        // Send a GET request to /api/post/{id}/upvoters
        $response = $this->actingAs($user)->getJson("/api/post/{$nonExistingPostId}/upvoters");

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
