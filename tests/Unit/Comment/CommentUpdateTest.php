<?php

namespace Tests\Unit;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_update_a_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->postJson('/api/comment', [
            'content' => 'This is a comment.',
            'post_id' => $post->id,
        ]);

        $response->assertStatus(201);

        $response = $this->actingAs($user, 'api')->patchJson("/api/comment/{$response->json('id')}", [
            'content' => 'This is an updated comment.',
        ]);

        $response->assertStatus(200);
    }

    public function test_a_user_cannot_update_a_comment_without_content()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->postJson('/api/comment', [
            'content' => 'This is a comment.',
            'post_id' => $post->id,
        ]);

        $response->assertStatus(201);

        $response = $this->actingAs($user, 'api')->patchJson("/api/comment/{$response->json('id')}", [
            'content' => '',
        ]);

        $response->assertStatus(422);
    }

    public function test_a_user_cannot_update_a_comment_that_does_not_belong_to_them()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = $post->comments()->create(['content' => 'This is a comment.', 'user_id' => $user->id]);

        $user2 = User::factory()->create();

        $response = $this->actingAs($user2, 'api')->patchJson("/api/comment/{$comment->id}", [
            'content' => 'This is an updated comment.',
        ]);

        $response->assertStatus(403);
    }

    public function test_a_guest_cannot_update_a_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $comment = $post->comments()->create(['content' => 'This is a comment.', 'user_id' => $user->id]);

        $response = $this->patchJson("/api/comment/{$comment->id}", [
            'content' => 'This is an updated comment.',
        ]);

        $response->assertStatus(401);
    }

    public function test_a_user_cannot_update_a_nonexistent_comment()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->patchJson("/api/comment/999", [
            'content' => 'This is an updated comment.',
        ]);

        $response->assertStatus(404);
    }
}
