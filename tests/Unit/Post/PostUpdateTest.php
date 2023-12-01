<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostUpdateTest extends TestCase
{
    private $route = '';

    use RefreshDatabase;

    public function test_update_successfully(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->patchJson("/api/post/{$post->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('Updated Title', $post->fresh()->title);
        $this->assertEquals('Updated Content', $post->fresh()->content);
    }

    public function test_update_fails_for_non_owner(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);
        $nonOwner = User::factory()->create();

        $response = $this->actingAs($nonOwner)->patchJson("/api/post/{$post->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ]);

        $response->assertStatus(403);
    }

    public function test_update_fails_for_guest(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->patchJson("/api/post/{$post->id}", [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ]);

        $response->assertStatus(401);
    }

    public function test_update_fails_for_post_not_found(): void
    {
        $owner = User::factory()->create();

        $response = $this->actingAs($owner)->patchJson("/api/post/1", [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ]);

        $response->assertStatus(404);
    }

    public function test_update_fails_for_invalid_title(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->patchJson("/api/post/{$post->id}", [
            'title' => '',
            'content' => 'Updated Content',
        ]);

        $response->assertStatus(422);
    }

    public function test_update_fails_for_invalid_content(): void
    {
        $owner = User::factory()->create();
        $post = Post::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->patchJson("/api/post/{$post->id}", [
            'title' => 'Updated Title',
            'content' => '',
        ]);

        $response->assertStatus(422);
    }
}
