<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityShowTest extends TestCase
{
    private $showRouteTemplate = '/api/community/%s';

    use RefreshDatabase;

    public function test_show_returns_the_specified_community(): void
    {
        $community = Community::factory()->create();

        $response = $this->get(sprintf($this->showRouteTemplate, $community->name));

        $response
            ->assertStatus(200)
            ->assertJson($community->toArray());
    }

    public function test_show_returns_not_found_for_invalid_name(): void
    {
        $invalidName = 'invalid-name';

        $response = $this->get(sprintf($this->showRouteTemplate, $invalidName));

        $response->assertStatus(404);
    }
}
