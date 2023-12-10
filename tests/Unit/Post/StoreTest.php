<?php

namespace Tests\Unit\Post;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreTest extends TestCase
{
    private $route = '/api/post';

    use RefreshDatabase;

    /** @test */
    public function test_store_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a community
        $community = Community::factory()->create();

        // Subscribe the user to the community
        $user->subscriptions()->attach($community->id);

        // Data to be sent with the POST request
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is a test post.',
            'community_id' => $community->id
        ];

        // Send a POST request to /api/post
        $response = $this->actingAs($user)->postJson($this->route, $postData);

        // Assert that the response has status code 201
        $response->assertStatus(201);

        // Assert that the response is a JSON object
        $response->assertJsonStructure([
            'id',
            'title',
            'content',
            'owner',
            'community',
            'created_at',
            'updated_at',
        ]);
    }

    /** @test */
    public function test_store_without_title(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Data to be sent with the POST request
        $data = [
            'content' => 'This is a test post.',
            'community_id' => Community::factory()->create()->id
        ];

        // Send a POST request to /api/post
        $response = $this->actingAs($user)->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response is a JSON object
        $response->assertJsonValidationErrors('title');
    }

    /** @test */
    public function test_store_without_content(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Data to be sent with the POST request
        $postData = [
            'title' => 'Test Post',
            'community_id' => Community::factory()->create()->id
        ];

        // Send a POST request to /api/post
        $response = $this->actingAs($user)->postJson($this->route, $postData);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response is a JSON object
        $response->assertJsonValidationErrors('content');
    }

    /** @test */
    public function test_store_without_community_id(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Data to be sent with the POST request
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is a test post.',
        ];

        // Send a POST request to /api/post
        $response = $this->actingAs($user)->postJson($this->route, $postData);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response is a JSON object
        $response->assertJsonValidationErrors('community_id');
    }

    /** @test */
    public function test_store_with_invalid_title(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Data to be sent with the POST request
        $postData = [
            'title' => '',
            'content' => 'This is a test post.',
            'community_id' => Community::factory()->create()->id
        ];

        // Send a POST request to /api/post
        $response = $this->actingAs($user)->postJson($this->route, $postData);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response is a JSON object
        $response->assertJsonValidationErrors('title');
    }

    /** @test */
    public function test_store_with_invalid_content(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Data to be sent with the POST request
        $data = [
            'title' => 'Test Post',
            'content' => '',
            'community_id' => Community::factory()->create()->id
        ];

        // Send a POST request to /api/post
        $response = $this->actingAs($user)->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response is a JSON object
        $response->assertJsonValidationErrors('content');
    }

    /** @test */
    public function test_store_with_invalid_community_id(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Data to be sent with the POST request
        $postData = [
            'title' => 'Test Post',
            'content' => 'This is a test post.',
            'community_id' => 999
        ];

        // Send a POST request to /api/post
        $response = $this->actingAs($user)->postJson($this->route, $postData);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response is a JSON object
        $response->assertJsonValidationErrors('community_id');
    }

    /** @test */
    public function test_store_without_being_subscribied(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a community
        $community = Community::factory()->create();

        // Data to be sent with the POST request
        $data = [
            'title' => 'Test Post',
            'content' => 'This is a test post.',
            'community_id' => $community->id
        ];

        // Send a POST request to /api/post
        $response = $this->actingAs($user)->postJson($this->route, $data);

        // Assert that the response has status code 403
        $response->assertStatus(403);

        // Assert that the response is a JSON object
        $response->assertJson(['error' => 'You are not subscribed to this community.']);
    }

    /** @test */
    public function test_store_while_banned(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a community
        $community = Community::factory()->create();

        // Subscribe the user to the community
        $user->subscriptions()->attach($community->id);

        // Ban the user from the community
        $community->bans()->attach($user->id);

        // Data to be sent with the POST request
        $data = [
            'title' => 'Test Post',
            'content' => 'This is a test post.',
            'community_id' => $community->id
        ];

        // Send a POST request to /api/post
        $response = $this->actingAs($user)->postJson($this->route, $data);

        // Assert that the response has status code 403
        $response->assertStatus(403);

        // Assert that the response is a JSON object
        $response->assertJson(['error' => 'You are banned from this community.']);
    }
}
