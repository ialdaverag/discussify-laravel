<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTest extends TestCase
{
    private $route = '/api/comment/%s';

    use RefreshDatabase;

    public function test_update_comment_successfully()
    {
        // Create a comment
        $comment = Comment::factory()->create();

        // Get the user and the community from the comment
        $user = $comment->user;

        // Create an array with the comment data
        $community = $comment->post->community;

        // Add the user to the community's subscribers
        $community->subscribers()->attach($user->id);

        // Create an array with the comment data
        $data = [
            'post_id' => $comment->post_id,
            'body' => 'This is a new comment',
        ];

        // Send a POST request to the API as the user
        $response = $this->actingAs($user)->patchJson(sprintf($this->route, $comment->id), $data);

        // Assert that the response has status code 201
        $response->assertStatus(200);
    }

    public function test_a_user_cannot_update_a_comment_without_content()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a comment
        $comment = Comment::factory()->create();

        // Create an array with the comment data
        $community = $comment->post->community;

        // Add the user to the community's subscribers
        $community->subscribers()->attach($user->id);

        // Data to be sent with the POST request
        $data = [
            'content' => '',
        ];

        // Send a request to the API as the user
        $response = $this->actingAs($user, 'api')->patchJson(sprintf($this->route, $comment->id), $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);
    }

    public function test_a_user_cannot_update_a_comment_that_does_not_belong_to_them()
    {
        // Create a comment
        $comment = Comment::factory()->create();

        // Get the community from the comment's post
        $community = $comment->post->community;

        // Create a new user
        $user = User::factory()->create();

        // Subscribe the new user to the community
        $community->subscribers()->attach($user->id);

        // Create an array with the comment data
        $data = [
            'content' => 'This is an updated comment.',
        ];

        // Try to update the comment as the new user
        $response = $this->actingAs($user)->patchJson(sprintf($this->route, $comment->id), $data);

        // Assert that the response has status code 403 (Forbidden)
        $response->assertStatus(403);
    }

    public function test_a_guest_cannot_update_a_comment()
    {
        // Create a comment
        $comment = Comment::factory()->create();

        // Create an array with the comment data
        $data = [
            'content' => 'This is an updated comment.',
        ];

        // Try to update the comment as a guest
        $response = $this->patchJson(sprintf($this->route, $comment->id), $data);

        // Assert that the response has status code 401 (Unauthorized)
        $response->assertStatus(401);
    }

    public function test_a_user_cannot_update_a_nonexistent_comment()
    {
        // Create a user
        $user = User::factory()->create();

        // Create an array with the comment data
        $data = [
            'content' => 'This is an updated comment.',
        ];

        // Try to update a nonexistent comment as the user
        $response = $this->actingAs($user)->patchJson(sprintf($this->route, 9999), $data);

        // Assert that the response has status code 404 (Not Found)
        $response->assertStatus(404);
    }
}
