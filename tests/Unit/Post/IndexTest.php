<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class IndexTest extends TestCase
{
    private $route = '/api/post';

    use RefreshDatabase;

    /** @test */
    public function test_index_successfully()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a community
        $community = Community::factory()->create();

        // Create 3 posts
        $post = Post::factory()->count(3)->create();

        // Send a GET request to /api/post
        $response = $this->actingAs($user)->getJson($this->route);

        // Assert that the response has status code 200
        $response->assertStatus(200);

        // Assert that the response is a JSON object
        $response->assertJsonStructure([
            '*' => [
                'id',
                'title',
                'content',
                'owner',
                'community',
                'created_at',
                'updated_at',
            ]
        ]);
    }
}
