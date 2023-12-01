<?php

namespace Tests\Unit\Post;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BookmarkTest extends TestCase
{
    use RefreshDatabase;

    public function test_bookmark_successfully(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->postJson("/api/post/{$post->id}/bookmark");

        $response->assertStatus(204);
    }

    public function test_bookmark_fails_for_guest(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);

        $response = $this->postJson("/api/post/{$post->id}/bookmark");

        $response->assertStatus(401);
    }

    public function test_bookmark_fails_for_post_not_found(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson("/api/post/1/bookmark");

        $response->assertStatus(404);
    }

    public function test_already_bookmarked(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $user->id]);
        $user->bookmarks()->attach($post);

        $response = $this->actingAs($user)->postJson("/api/post/{$post->id}/bookmark");

        $response->assertStatus(400);
    }
}
