<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    private $route = '/api/comment/%s';

    use RefreshDatabase;

    public function test_show_returns_a_comment()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create(['user_id' => $user->id]);

        // Create a comment
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        // Send a request to the API as the user
        $response = $this->actingAs($user)->getJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 200
        $response->assertStatus(200);
    }

    public function test_show_comment_not_found()
    {
        // Create a user
        $user = User::factory()->create();

        // Send a request to the API as the user
        $response = $this->actingAs($user)->getJson(sprintf($this->route, 1));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
