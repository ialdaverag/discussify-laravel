<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUpvotersTest extends TestCase
{
    private $route = '/api/comment/%s/upvoters';

    use RefreshDatabase;

    public function test_get_upvoters_successfully()
    {
        // Create a comment
        $comment = Comment::factory()->create();

        // Send a request to the API as the user
        $response = $this->getJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 200
        $response->assertStatus(200);
    }

    public function test_get_upvoters_for_non_existing_comment()
    {
        // ID of a comment that doesn't exist
        $nonExistingCommentId = 123456; 

        // Send a request to the API as the user
        $response = $this->getJson(sprintf($this->route, $nonExistingCommentId));

        // Assert that the response has status code 404
        $response->assertStatus(404);
    }
}
