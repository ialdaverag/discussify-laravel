<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndexTest extends TestCase
{
    private $route = '/api/comment';

    use RefreshDatabase;

    public function test_index_returns_all_comments()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a comment
        $comments = Comment::factory()->count(3)->create();

        // Send a request to the API as the user
        $response = $this->actingAs($user)->getJson($this->route);

        // Assert that the response has status code 200
        $response->assertStatus(200);
    }
}
