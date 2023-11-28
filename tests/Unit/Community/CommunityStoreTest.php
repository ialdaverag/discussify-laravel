<?php

namespace Tests\Unit\Community;

use App\Models\User;
use App\Models\Community;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CommunityStoreTest extends TestCase
{
    private $route = '/api/community';

    use RefreshDatabase;
    /**
     * A basic unit test example.
     */
    public function test_store_successfully(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $communityData = [
            'name' => 'TestCommunity',
            'about' => 'This is a test community.',
        ];

        $response = $this->postJson($this->route, $communityData);
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
        $user = User::factory()->create();
        $this->actingAs($user);

        $communityData = [
            'about' => 'This is a test community.',
        ];

        $response = $this->postJson($this->route, $communityData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_store_with_duplicate_name(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $community = Community::factory()->create(['name' => 'TestCommunity']);

        $communityData = [
            'name' => 'Test Community',
            'about' => 'This is a test community.',
        ];

        $response = $this->postJson($this->route, $communityData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    public function test_store_with_invalid_name(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $communityData = [
            'name' => 'Test Community', 
            'about' => 'This is a test community.',
        ];

        $response = $this->postJson($this->route, $communityData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }
}
