<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnbookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_unbookmark_successfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $user->bookmarks()->attach($post);

        $response = $this->actingAs($user)->postJson("/api/post/{$post->id}/unbookmark");

        $response->assertStatus(204);
    }

    public function test_unbookmark_fails_for_guest(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $user->bookmarks()->attach($post);

        $response = $this->postJson("/api/post/{$post->id}/unbookmark");

        $response->assertStatus(401);
    }

    public function test_unbookmark_fails_for_post_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/post/1/unbookmark");

        $response->assertStatus(404);
    }

    public function test_not_bookmarked(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson("/api/post/{$post->id}/unbookmark");

        $response->assertStatus(400);
    }
}
