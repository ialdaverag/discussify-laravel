<?php

namespace Tests\Unit\Post;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_comments_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Create 3 comments for the post
        $comments = Comment::factory()->count(3)->create(['post_id' => $post->id]);

        // Send a GET request to /api/post/{id}/comments
        $response = $this->actingAs($user)->getJson("/api/post/{$post->id}/comments");

        // Assert that the response has status code 200
        $response->assertStatus(200);

        // Assert that the comments count is 3
        $response->assertJsonCount(3);
    }

    public function test_get_comments_for_non_existing_post()
    {
        // Create a user
        $user = User::factory()->create();

        // ID of a post that doesn't exist
        $nonExistingPostId = 123456; 

        // Send a GET request to /api/post/{id}/comments
        $response = $this->actingAs($user)->getJson("/api/post/{$nonExistingPostId}/comments");

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
