<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_all_comments()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comments = Comment::factory()->count(3)->create(['post_id' => $post->id]);

        $response = $this->actingAs($user)->getJson('/api/comment');

        $response->assertStatus(200);
    }
}
