<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribeTest extends TestCase
{
    private $route = '/api/community/%s/subscribe';

    use RefreshDatabase;

    public function test_subscribe_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a community
        $community = Community::factory()->create();

        // Send a POST request to /api/community/{community}/subscribe
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $community->name));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }

    public function test_community_not_found(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Send a POST request to /api/community/{community}/subscribe
        $response = $this->actingAs($user)->postJson(sprintf($this->route, 'nonexistent'));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }

    public function test_banned_user_cannot_subscribe_to_community(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a community
        $community = Community::factory()->create();

        // Ban the user from the community
        $community->bans()->attach($user->id);

        // Send a POST request to /api/community/{community}/subscribe
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $community->name));

        // Assert that the response has status code 400
        $response->assertStatus(400);

        // Assert that the response has the correct error message
        $response->assertJson(['error' => 'You are banned from this community.']);
    }

    public function test_user_cannot_subscribe_to_same_community_twice(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a community
        $community = Community::factory()->create();

        // Subscribe the user to the community
        $community->subscribers()->attach($user->id);

        // Send a POST request to /api/community/{community}/subscribe
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $community->name));

        // Assert that the response has status code 409
        $response->assertStatus(409);

        // Assert that the response has the correct error message
        $response->assertJson(['error' => 'You are already subscribed to this community.']);
    }
}
