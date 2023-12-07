<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetBans extends TestCase
{
    use RefreshDatabase;

    private $route = '/api/community/%s/bans';

    public function test_get_bans_successfully()
    {
        // Create a owner
        $owner = User::factory()->create();

        // Create a community
        $community = Community::factory()->create(['user_id' => $owner->id]);

        // Create 3 bans
        $bans = User::factory()->count(3)->create();

        // Attach the bans to the community
        $community->bans()->attach($bans->pluck('id'));

        // Send a GET request to /api/community/{community}/bans
        $response = $this->actingAs($owner)->getJson(sprintf($this->route, $community->name));

        // Assert that the response has status code 200
        $response->assertStatus(200);
    }

    public function test_community_not_found()
    {
        // Create a user
        $user = User::factory()->create();

        // Send a GET request to /api/community/{community}/bans
        $response = $this->actingAs($user)->getJson(sprintf($this->route, 'nonexistent_community'));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
