<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnbanTest extends TestCase
{
    use RefreshDatabase;

    private $route = '/api/community/%s/unban/%s';

    public function test_unban_successfully()
    {
        // Create a owner
        $owner = User::factory()->create();

        // Create a community
        $community = Community::factory()->create(['user_id' => $owner->id]);

        // Make the user a moderator of the community
        $community->moderators()->attach($owner->id);

        // Create a user
        $user = User::factory()->create();

        // Ban the user from the community
        $community->bans()->attach($user->id);

        // Send a POST request to /api/community/{community}/ban/{user}
        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, $user->username));

        // Assert that the response has status code 204
        $response->assertStatus(204);
    }

    public function test_community_not_found()
    {
        // Create a user
        $user = User::factory()->create();

        // Send a POST request to /api/community/{community}/ban/{user}
        $response = $this->actingAs($user)->postJson(sprintf($this->route, 'nonexistent_community', $user->username));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }

    public function test_user_not_found()
    {
        // Create a owner
        $owner = User::factory()->create();

        // Create a community
        $community = Community::factory()->create(['user_id' => $owner->id]);

        // Unban a nonexistent user
        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, 'nonexistent_user'));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }

    public function test_only_moderators_can_unban_users()
    {
        // Create a owner
        $owner = User::factory()->create();

        // Create a community
        $community = Community::factory()->create(['user_id' => $owner->id]);

        // Create a user
        $user = User::factory()->create();

        // Ban the user from the community
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $community->name, $user->username));

        // Assert that the response has status code 403
        $response->assertStatus(403);
    }

    public function test_user_not_banned_from_community()
    {
        // Create a owner
        $owner = User::factory()->create();

        // Create a community
        $community = Community::factory()->create(['user_id' => $owner->id]);

        // Create a user
        $user = User::factory()->create();

        // Make the user a subscriber of the community
        $community->subscribers()->attach($user->id);

        // Make the user a moderator of the community
        $community->moderators()->attach($owner->id);

        // Send a POST request to /api/community/{community}/ban/{user}
        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, $user->username));

        // Assert that the response has status code 400
        $response->assertStatus(400);
    }
}
