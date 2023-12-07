<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BanTest extends TestCase
{
    private $route = '/api/community/%s/ban/%s';

    use RefreshDatabase;

    public function test_ban_successfully()
    {
        // Create a community
        $owner = User::factory()->create();

        // Create a community
        $community = Community::factory()->create(['user_id' => $owner->id]);

        // Make the user a moderator of the community
        $community->moderators()->attach($owner->id);

        // Create a user
        $user = User::factory()->create();

        // Subscribe the user to the community
        $community->subscribers()->attach($user->id);

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
        // Create a community
        $owner = User::factory()->create();

        // Create a community
        $community = Community::factory()->create(['user_id' => $owner->id]);

        // Make the user a moderator of the community
        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, 'nonexistent_user'));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }

    public function test_only_moderators_can_ban_users()
    {
        // Create a community
        $owner = User::factory()->create();

        // Create a community
        $community = Community::factory()->create(['user_id' => $owner->id]);

        // Make the user a moderator of the community
        $user = User::factory()->create();

        // Subscribe the user to the community
        $response = $this->actingAs($user)->postJson(sprintf($this->route, $community->name, $user->username));

        // Assert that the response has status code 403
        $response->assertStatus(403);
    }

    public function test_user_is_not_subscribed_to_community()
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);
        $community->moderators()->attach($owner->id);

        $user = User::factory()->create();

        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, $user->username));
        $response->assertStatus(400);
    }

    public function test_user_is_already_banned_from_community()
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);
        $community->moderators()->attach($owner->id);

        $user = User::factory()->create();

        $community->subscribers()->attach($user->id);
        $community->bans()->attach($user->id);

        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, $user->username));
        $response->assertStatus(400);
    }
}
