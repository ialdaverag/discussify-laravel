<?php

namespace Tests\Unit\Community;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Community;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private $route = '/api/community';

    use RefreshDatabase;

    /**
     * test index returns all communities
     * 
     * @return void
     */
    public function test_index_returns_all_communities(): void
    {
        // Create 3 communities
        $communities = Community::factory()->count(3)->create();

        // Send a GET request to /api/community
        $response = $this->get($this->route);

        // Assert that the response has status code 200
        $response->assertStatus(200);

        // Assert that the response is a JSON array
        $response->assertJsonCount(3);

        // Assert that the response JSON matches the created communities
        $response->assertJsonStructure([
                '*' => [
                    'id',
                    'name',
                    'about',
                    'owner',
                    'created_at',
                    'updated_at',
                ]
            ]);
    }
}
