<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentCreateTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_user_can_create_a_comment()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
     
        $response = $this->actingAs($user, 'api')->postJson('/api/comment', [
            'content' => 'This is a comment.',
            'post_id' => $post->id,
        ]);
     
        $response->assertStatus(201);
    }

    public function test_a_user_can_create_a_reply()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
     
        $response = $this->actingAs($user, 'api')->postJson('/api/comment', [
            'content' => 'This is a comment.',
            'post_id' => $post->id,
        ]);
     
        $response->assertStatus(201);
     
        $response = $this->actingAs($user, 'api')->postJson('/api/comment', [
            'content' => 'This is a reply.',
            'post_id' => $post->id,
            'comment_id' => $response->json('id'),
        ]);
     
        $response->assertStatus(201);
    }

    public function test_a_user_cannot_create_a_comment_without_content()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
     
        $response = $this->actingAs($user, 'api')->postJson('/api/comment', [
            'post_id' => $post->id,
        ]);
     
        $response->assertStatus(422);
    }

    public function test_a_user_cannot_create_a_comment_without_a_post_id()
    {
        $user = User::factory()->create();
     
        $response = $this->actingAs($user, 'api')->postJson('/api/comment', [
            'content' => 'This is a comment.',
        ]);
     
        $response->assertStatus(422);
    }

    public function test_a_user_cannot_create_a_comment_with_more_than_1000_characters()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->postJson('/api/comment', [
            'content' => str_repeat('a', 1001),
            'post_id' => $post->id,
        ]);

        $response->assertStatus(422);
    }

    public function test_a_user_cannot_create_a_comment_with_less_than_one_character()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user, 'api')->postJson('/api/comment', [
            'content' => '',
            'post_id' => $post->id,
        ]);

        $response->assertStatus(422);
    }
    
    public function test_a_user_cannot_create_a_comment_with_an_invalid_post_id()
    {
        $user = User::factory()->create();
     
        $response = $this->actingAs($user, 'api')->postJson('/api/comment', [
            'content' => 'This is a comment.',
            'post_id' => 999,
        ]);
     
        $response->assertStatus(422);
    }
}
