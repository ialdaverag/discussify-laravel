<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddModeratorTest extends TestCase
{
    private $route = '/api/community/%s/mod/%s';

    use RefreshDatabase;

    public function test_owner_can_add_moderators()
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);

        $user = User::factory()->create();

        $community->subscribers()->attach($user->id);

        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, $user->username));
        $response->assertStatus(204);
    }

    public function test_community_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(sprintf($this->route, 'nonexistent_community', $user->username));
        $response->assertStatus(404);
    }

    public function test_user_not_found()
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);

        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, 'nonexistent_user'));
        $response->assertStatus(404);
    }

    public function test_only_owner_can_add_moderators()
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(sprintf($this->route, $community->name, $user->username));
        $response->assertStatus(403);
    }

    public function test_user_not_subscribed_to_community()
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);

        $user = User::factory()->create();

        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, $user->username));
        $response->assertStatus(400);
    }

    public function test_user_already_moderator()
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);

        $user = User::factory()->create();

        $community->subscribers()->attach($user->id);
        $community->moderators()->attach($user->id);

        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, $user->username));
        $response->assertStatus(400);
    }
}
