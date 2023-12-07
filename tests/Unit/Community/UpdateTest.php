<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_successfully(): void
    {
        // Create a community
        $community = Community::factory()->create();

        // Get the owner of the community
        $owner = $community->user;

        // Data to update the community with
        $data = [
            'name' => 'UpdatedName',
            'description' => 'UpdatedDescription',
        ];

        // Send a PATCH request to /api/community/{community}
        $response = $this->actingAs($owner)->patchJson("/api/community/{$community->name}", $data);

        // Assert that the response has status code 200
        $response->assertStatus(200);
    }

    public function test_update_fails_for_non_owner(): void
    {
        // Create a community
        $community = Community::factory()->create();

        // Create a non-owner user
        $nonOwner = User::factory()->create();

        // Data to update the community with
        $data = [
            'name' => 'UpdatedName',
            'description' => 'Updated Description.',
        ];

        // Send a PATCH request to /api/community/{community}
        $response = $this->actingAs($nonOwner)->patchJson("/api/community/{$community->name}", $data);

        // Assert that the response has status code 403
        $response->assertStatus(403);
    }

    public function test_update_fails_for_non_existent_community(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Non-existent community name
        $community = 'non-existent-community';

        // Data to update the community with
        $data = [
            'name' => 'UpdatedName',
        ];

        // Send a PATCH request to /api/community/{community}
        $response = $this->actingAs($user)->patchJson("/api/community/{$community}", $data);

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }

    public function test_update_fails_for_duplicate_name(): void
    {
        // Create a community
        $community = Community::factory()->create();

        // Get the owner of the community
        $owner = $community->user;

        // Create another community
        $data = [
            'name' => $community->name,
        ];

        // Send a PATCH request to /api/community/{community}
        $response = $this->actingAs($owner)->patchJson("/api/community/{$community->name}", $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response has a validation error for the name field
        $response->assertJsonValidationErrors('name');
    }
}
