<?php

namespace Tests\Unit\Community;

use App\Models\User;
use App\Models\Community;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreTest extends TestCase
{
    private $route = '/api/community';

    use RefreshDatabase;

    /**
     * test store successfully
     * 
     * @return void
     */
    public function test_store_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Data to be sent with the request
        $data = [
            'name' => 'TestCommunity',
            'about' => 'This is a test community.',
        ];

        // Send a POST request to /api/community
        $response = $this->actingAs($user)->postJson($this->route, $data);

        // Assert that the response has status code 201
        $response
            ->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'about',
                'owner',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_store_without_name(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Data to be sent with the request
        $data = [
            'about' => 'This is a test community.',
        ];

        // Send a POST request to /api/community
        $response = $this->actingAs($user)->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_store_with_duplicate_name(): void
    {
        // Create a community
        $community = Community::factory()->create(['name' => 'TestCommunity']);
        
        // Create a user
        $user = User::factory()->create();

        // Data to be sent with the request
        $data = [
            'name' => 'TestCommunity',
            'about' => 'This is a test community.',
        ];

        // Send a POST request to /api/community
        $response = $this->actingAs($user)->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }

    public function test_store_with_invalid_name(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Data to be sent with the request
        $data = [
            'name' => 'Test Community', 
            'about' => 'This is a test community.',
        ];

        // Send a POST request to /api/community
        $response = $this->actingAs($user)->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('name');
    }
}
