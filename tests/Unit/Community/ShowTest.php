<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    private $route = '/api/community/%s';

    use RefreshDatabase;

    public function test_show_returns_the_specified_community(): void
    {   
        // Create a community
        $community = Community::factory()->create();

        // Send a GET request to /api/community/{community}
        $response = $this->get(sprintf($this->route, $community->name));

        // Assert that the response has status code 200
        $response->assertStatus(200);

        // Assert that the response is a JSON object
        $response->assertJson([
                'id' => $community->id,
                'name' => $community->name,
                'about' => $community->about,
                'owner' => [
                    'id' => $community->user_id,
                    'username' => $community->user->username,
                    'email' => $community->user->email,
                    'created_at' => $community->user->created_at->toJSON(),
                    'updated_at' => $community->user->updated_at->toJSON(),
                ],
                'created_at' => $community->created_at->toJSON(),
                'updated_at' => $community->updated_at->toJSON(),
            ]);
    }

    public function test_not_found(): void
    {
        // Name of a non-existent community
        $name = 'nonexistent';

        // Send a GET request to /api/community/{community}
        $response = $this->get(sprintf($this->route, $name));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
