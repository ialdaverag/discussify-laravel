<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunityIndexTest extends TestCase
{
    private $route = '/api/community';

    use RefreshDatabase;

    public function test_index_returns_all_communities(): void
    {
        $communities = Community::factory()->count(3)->create();

        $response = $this->get($this->route);
        $response
            ->assertStatus(200)
            ->assertJson($communities->toArray());
    }
}
