<?php

namespace Tests\Unit\Community;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPostsTest extends TestCase
{
    private $route = '/api/community/%s/posts';

    use RefreshDatabase;

    public function test_get_posts_successfully()
    {
        // Create a community
        $community = Community::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Attach the post to the community
        $community->posts()->save($post);

        // Send a GET request to /api/community/{community}/posts
        $response = $this->getJson(sprintf($this->route, $community->name));

        // Assert that the response has status code 200
        $response->assertStatus(200);
    }

    public function test_get_posts_for_non_existing_community()
    {
        // Name of a non-existing community
        $name = 'nonexisting';

        // Send a GET request to /api/community/{community}/posts
        $response = $this->getJson(sprintf($this->route, $name));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
