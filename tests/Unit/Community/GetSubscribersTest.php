<?php

namespace Tests\Unit;

use App\Models\Community;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetSubscribersTest extends TestCase
{
    use RefreshDatabase;

    private $route = '/api/community/%s/subscribers';

    public function test_get_subscribers_successfully()
    {
        // Create a community
        $community = Community::factory()->create();

        // Create 3 subscribers
        $subscribers = User::factory()->count(3)->create();

        // Attach the subscribers to the community
        $community->subscribers()->attach($subscribers->pluck('id'));

        // Send a GET request to /api/community/{community}/subscribers
        $response = $this->getJson(sprintf($this->route, $community->name));

        // Assert that the response has status code 200
        $response->assertStatus(200);
    }

    public function test_community_not_found()
    {   
        // Name of a non-existent community
        $community = 'nonexistent';

        // Send a GET request to /api/community/{community}/subscribers
        $response = $this->getJson(sprintf($this->route, $community));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
