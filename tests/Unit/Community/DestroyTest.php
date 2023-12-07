<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyTest extends TestCase
{
    private $route = '/api/community/%s';

    use RefreshDatabase;

    public function test_destroy_successfully(): void
    {
        // Create a community
        $community = Community::factory()->create();

        // Get the owner of the community
        $owner = $community->user;

        // Send a DELETE request to /api/community/{community}
        $response = $this->actingAs($owner)->deleteJson(sprintf($this->route, $community->name));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }

    public function test_destroy_fails_for_non_owner(): void
    {
        // Create a community
        $community = Community::factory()->create();

        // Create a non-owner user
        $nonOwner = User::factory()->create();

        // Send a DELETE request to /api/community/{community}
        $response = $this->actingAs($nonOwner)->deleteJson(sprintf($this->route, $community->name));

        // Assert that the response has status code 403
        $response->assertStatus(403);
    }

    public function test_destroy_fails_for_non_existent_community(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Name of a non-existent community
        $name = 'nonexistent';

        // Send a DELETE request to /api/community/{community}
        $response = $this->actingAs($user)->deleteJson(sprintf($this->route, $name));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
