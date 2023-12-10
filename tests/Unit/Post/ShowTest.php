<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTest extends TestCase
{
    private $route = '/api/post/%s';

    use RefreshDatabase;

    /** @test */
    public function test_show_successfully()
    {
        // Create a post
        $post = Post::factory()->create();

        // Send a GET request to /api/post/{id}
        $response = $this->getJson(sprintf($this->route, $post->id));

        // Assert that the response has status code 200
        $response->assertStatus(200);

        // Assert that the response is a JSON object
        $response->assertJsonStructure([
            'id',
            'title',
            'content',
            'owner',
            'community',
            'created_at',
            'updated_at',
        ]);
    }

    /** @test */
    public function test_post_not_found()
    {
        // Send a GET request to /api/post/{id}
        $response = $this->getJson(sprintf($this->route, 1));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
