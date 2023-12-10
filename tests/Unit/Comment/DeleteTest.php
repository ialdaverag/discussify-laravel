<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTest extends TestCase
{
    private $route = '/api/comment/%s';

    use RefreshDatabase;

    public function test_delete_comment_successfully()
    {
        // Create a comment
        $comment = Comment::factory()->create();

        // Get the user from the comment
        $user = $comment->user;

        // Get the community from the comment's post
        $community = $comment->post->community;

        // Subscribe the user to the community
        $community->subscribers()->attach($user->id);

        // Try to delete the comment as the user
        $response = $this->actingAs($user)->deleteJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 200 (OK)
        $response->assertStatus(204);
    }

    public function test_a_moderator_can_delete_a_comment(): void
    {
        // Create a comment
        $comment = Comment::factory()->create();

        // Get the community from the comment's post
        $community = $comment->post->community;

        // Create a new user
        $moderator = User::factory()->create();

        // Make the new user a moderator of the community
        $community->moderators()->attach($moderator->id);

        // Try to delete the comment as the moderator
        $response = $this->actingAs($moderator)->deleteJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 204 (No Content)
        $response->assertStatus(204);
    }

    public function test_a_user_cannot_delete_a_comment_that_does_not_belong_to_them()
    {
        // Create a comment
        $comment = Comment::factory()->create();

        // Get the user from the comment
        $user = User::factory()->create();

        // Get the community from the comment's post
        $community = $comment->post->community;

        // Subscribe the user to the community
        $community->subscribers()->attach($user->id);

        // Try to delete the comment as the user
        $response = $this->actingAs($user)->deleteJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 200 (OK)
        $response->assertStatus(403);
    }

    public function test_a_guest_cannot_delete_a_comment()
    {
        // Create a comment
        $comment = Comment::factory()->create();

        // Try to delete the comment as the user
        $response = $this->deleteJson(sprintf($this->route, $comment->id));

        // Assert that the response has status code 401 (Unauthorized)
        $response->assertStatus(401);
    }

    public function test_a_user_cannot_delete_a_comment_that_does_not_exist()
    {
        // Create a user
        $user = User::factory()->create();

        // Try to delete the comment as the user
        $response = $this->actingAs($user)->deleteJson(sprintf($this->route, 1));

        // Assert that the response has status code 404 (Not Found)
        $response->assertStatus(404);
    }
}
