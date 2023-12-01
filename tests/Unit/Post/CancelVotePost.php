<?php

namespace Tests\Unit\Post;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CancelVotePost extends TestCase
{
    use RefreshDatabase;

    public function test_cancel_vote_successfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $user->votes()->attach($post, ['direction' => 1]);

        $response = $this->actingAs($user)->postJson("/api/post/{$post->id}/vote/cancel");

        $response->assertStatus(204);
    }

    public function test_cancel_vote_fails_for_guest(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $user->votes()->attach($post, ['direction' => 1]);

        $response = $this->postJson("/api/post/{$post->id}/vote/cancel");

        $response->assertStatus(401);
    }

    public function test_cancel_vote_fails_for_post_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/post/1/vote/cancel");

        $response->assertStatus(404);
    }

    public function test_not_voted(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson("/api/post/{$post->id}/vote/cancel");

        $response->assertStatus(400);
    }
}
