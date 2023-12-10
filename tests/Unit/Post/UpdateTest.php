<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UpdateTest extends TestCase
{
    private $route = '/api/post/%s';

    use RefreshDatabase;

    public function test_update_successfully(): void
    {
        // Create a user
        $owner = User::factory()->create();

        // Create a post
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // Data to be sent with the PATCH request
        $data = [
            'title' => 'Updated Title',
            'content' => 'Updated Content.',
        ];

        // Send a PATCH request to /api/post/{id}
        $response = $this->actingAs($owner)->patchJson("/api/post/{$post->id}", $data);

        // Assert that the response has status code 200
        $response->assertStatus(200);
    }

    public function test_update_fails_for_non_owner(): void
    {
        // Create a user
        $owner = User::factory()->create();

        // Create a post
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // Create another user
        $nonOwner = User::factory()->create();

        // Data to be sent with the PATCH request
        $data = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ];

        // Send a PATCH request to /api/post/{id}
        $response = $this->actingAs($nonOwner)->patchJson("/api/post/{$post->id}", $data);

        // Assert that the response has status code 403
        $response->assertStatus(403);
    }

    public function test_update_fails_for_guest(): void
    {
        // Create a post
        $owner = User::factory()->create();

        // Create a post
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // Data to be sent with the PATCH request
        $data = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ];

        // Send a PATCH request to /api/post/{id}
        $response = $this->patchJson("/api/post/{$post->id}", $data);

        // Assert that the response has status code 401
        $response->assertStatus(401);
    }

    public function test_update_fails_for_post_not_found(): void
    {
        // Create a user
        $owner = User::factory()->create();

        // Data to be sent with the PATCH request
        $data = [
            'title' => 'Updated Title',
            'content' => 'Updated Content',
        ];

        // Send a PATCH request to /api/post/{id}
        $response = $this->actingAs($owner)->patchJson("/api/post/1", $data);

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }

    public function test_update_fails_for_invalid_title(): void
    {
        // Create a user
        $owner = User::factory()->create();

        // Create a post
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // Data to be sent with the PATCH request
        $data = [
            'title' => '',
            'content' => 'Updated Content',
        ];

        // Send a PATCH request to /api/post/{id}
        $response = $this->actingAs($owner)->patchJson("/api/post/{$post->id}", $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);
    }

    public function test_update_fails_for_invalid_content(): void
    {
        // Create a user
        $owner = User::factory()->create();

        // Create a post
        $post = Post::factory()->create(['user_id' => $owner->id]);

        // Data to be sent with the PATCH request
        $data = [
            'title' => 'Updated Title',
            'content' => '',
        ];

        // Send a PATCH request to /api/post/{id}
        $response = $this->actingAs($owner)->patchJson("/api/post/{$post->id}", $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);
    }
}
