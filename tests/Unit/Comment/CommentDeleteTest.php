<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentDeleteTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_delete_a_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->postJson('/api/comment', [
            'content' => 'This is a comment.',
            'post_id' => $post->id,
        ]);

        $response->assertStatus(201);

        $response = $this->actingAs($user, 'api')->deleteJson("/api/comment/{$response->json('id')}");

        $response->assertStatus(204);
    }

    public function test_a_user_cannot_delete_a_comment_that_does_not_belong_to_them()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = $post->comments()->create(['content' => 'This is a comment.', 'user_id' => $user->id]);

        $user2 = User::factory()->create();

        $response = $this->actingAs($user2, 'api')->deleteJson("/api/comment/{$comment->id}");

        $response->assertStatus(403);
    }

    public function test_a_guest_cannot_delete_a_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = $post->comments()->create(['content' => 'This is a comment.', 'user_id' => $user->id]);

        $response = $this->deleteJson("/api/comment/{$comment->id}");

        $response->assertStatus(401);
    }

    public function test_a_user_cannot_delete_a_comment_that_does_not_exist()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->deleteJson('/api/comment/1');

        $response->assertStatus(404);
    }
}
