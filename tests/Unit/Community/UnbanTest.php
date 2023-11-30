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
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);

        $user = User::factory()->create();

        $community->subscribers()->attach($user->id);
        $community->moderators()->attach($owner->id);
        $community->bans()->attach($user->id);

        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, $user->username));
        $response->assertStatus(204);

        $this->assertFalse($community->bans()->where('user_id', $user->id)->exists());
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

    public function test_only_moderators_can_unban_users()
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);

        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(sprintf($this->route, $community->name, $user->username));
        $response->assertStatus(403);
    }

    public function test_user_not_banned_from_community()
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);

        $user = User::factory()->create();

        $community->subscribers()->attach($user->id);
        $community->moderators()->attach($owner->id);

        $response = $this->actingAs($owner)->postJson(sprintf($this->route, $community->name, $user->username));
        $response->assertStatus(400);
    }
}
