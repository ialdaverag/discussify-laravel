<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetModerators extends TestCase
{
    use RefreshDatabase;

    private $route = '/api/community/%s/moderators';

    public function test_get_moderators_successfully()
    {
        // Create a community
        $owner = User::factory()->create();

        // Create a community
        $community = Community::factory()->create(['user_id' => $owner->id]);

        // Create 3 moderators
        $moderators = User::factory()->count(3)->create();

        // Attach the moderators to the community
        $community->moderators()->attach($moderators->pluck('id'));

        // Send a GET request to /api/community/{community}/moderators
        $response = $this->actingAs($owner)->getJson(sprintf($this->route, $community->name));

        // Assert that the response has status code 200
        $response->assertStatus(200);
    }

    public function test_community_not_found()
    {
        // Create a user
        $user = User::factory()->create();

        // Send a GET request to /api/community/{community}/moderators
        $response = $this->actingAs($user)->getJson(sprintf($this->route, 'nonexistent_community'));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
