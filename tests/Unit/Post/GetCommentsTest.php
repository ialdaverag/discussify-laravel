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
        $user = User::factory()->create();
        $post = Post::factory()->create();
        $comments = Comment::factory()->count(3)->create(['post_id' => $post->id]);

        $response = $this->actingAs($user)->getJson("/api/post/{$post->id}/comments");

        $response->assertStatus(200);
        $response->assertJsonCount(3);
    }

    public function test_get_comments_for_non_existing_post()
    {
        $user = User::factory()->create();
        $nonExistingPostId = 123456; // Use an ID that doesn't exist in your database

        $response = $this->actingAs($user)->getJson("/api/post/{$nonExistingPostId}/comments");

        $response->assertStatus(404);
    }
}
