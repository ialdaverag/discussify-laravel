<?php

namespace Tests\Unit\Post;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DownvoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_downvote_successfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson("/api/post/{$post->id}/vote/down");

        $response->assertStatus(204);
    }

    public function test_downvote_fails_for_guest(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson("/api/post/{$post->id}/vote/down");

        $response->assertStatus(401);
    }

    public function test_downvote_fails_for_post_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/post/1/vote/down");

        $response->assertStatus(404);
    }

    public function test_already_downvoted(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $user->votes()->attach($post, ['direction' => -1]);

        $response = $this->actingAs($user)->postJson("/api/post/{$post->id}/vote/down");

        $response->assertStatus(400);
    }

    public function test_upvoted(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $user->votes()->attach($post, ['direction' => 1]);

        $response = $this->actingAs($user)->postJson("/api/post/{$post->id}/vote/down");

        $response->assertStatus(200);
    }
}
