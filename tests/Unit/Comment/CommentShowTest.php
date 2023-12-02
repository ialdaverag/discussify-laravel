<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentShowTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_returns_a_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $response = $this->actingAs($user)->getJson("/api/comment/{$comment->id}");

        $response->assertStatus(200);
    }

    public function test_show_comment_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/comment/1');

        $response->assertStatus(404);
    }
}
