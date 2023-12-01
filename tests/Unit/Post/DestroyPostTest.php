<?php

namespace Tests\Unit\Post;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DestroyPostTest extends TestCase
{
    private $route = '';

    use RefreshDatabase;

    public function test_destroy_successfully(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->deleteJson("/api/post/{$post->id}");

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    public function test_destroy_fails_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);
        $nonOwner = User::factory()->create();

        $response = $this->actingAs($nonOwner)->deleteJson("/api/post/{$post->id}");

        $response->assertStatus(403);
    }

    public function test_destroy_fails_for_guest(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->deleteJson("/api/post/{$post->id}");

        $response->assertStatus(401);
    }

    public function test_destroy_fails_for_post_not_found(): void
    {
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->deleteJson("/api/post/1");

        $response->assertStatus(404);
    }
}
