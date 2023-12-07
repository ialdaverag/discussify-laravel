<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnsubscribeTest extends TestCase
{
    private $subscribeRoute = '/api/community/%s/subscribe';
    private $unsubscribeRoute = '/api/community/%s/unsubscribe';

    use RefreshDatabase;

    public function test_successfully(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a community
        $community = Community::factory()->create();

        // Subscribe the user to the community
        $community->subscribers()->attach($user->id);

        // Send a POST request to /api/community/{community}/unsubscribe
        $response = $this->actingAs($user)->postJson(sprintf($this->unsubscribeRoute, $community->name));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }

    public function test_user_cannot_unsubscribe_from_community_not_subscribed_to(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Create a community
        $community = Community::factory()->create();

        // Send a POST request to /api/community/{community}/unsubscribe
        $response = $this->actingAs($user)->postJson(sprintf($this->unsubscribeRoute, $community->name));

        // Assert that the response has status code 400
        $response->assertStatus(400);
    }

    public function test_user_cannot_unsubscribe_to_nonexistent_community(): void
    {
        // Create a user
        $user = User::factory()->create();

        // Send a POST request to /api/community/{community}/unsubscribe
        $response = $this->actingAs($user)->postJson(sprintf($this->unsubscribeRoute, 'nonexistent'));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
