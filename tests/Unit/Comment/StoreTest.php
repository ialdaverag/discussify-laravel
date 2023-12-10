<?php

namespace Tests\Unit\Comment;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    private $route = '/api/comment';

    use RefreshDatabase;

    public function test_a_user_can_create_a_comment()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Attach the user to the community's subscribers
        $post->community->subscribers()->attach($user->id);

        // Create a comment
        $data = [
            'content' => 'This is a comment.',
            'post_id' => $post->id,
        ];
     
        // Send a request to the API as the user
        $response = $this->actingAs($user)->postJson($this->route, $data);
     
        // Assert that the response has status code 201
        $response->assertStatus(201);
    }

    public function test_a_user_can_create_a_reply()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create(['user_id' => $user->id]);

        // Attach the user to the community's subscribers
        $post->community->subscribers()->attach($user->id);

        // Data to be sent with the POST request
        $data = [
            'content' => 'This is a comment.',
            'post_id' => $post->id,
        ];
     
        // Send a request to the API as the user
        $response = $this->actingAs($user)->postJson('/api/comment', $data);
     
        // Assert that the response has status code 201
        $response->assertStatus(201);
     
        // Get the comment from the response
        $response = $this->actingAs($user)->postJson('/api/comment', $data);
     
        // Assert that the response has status code 201
        $response->assertStatus(201);
    }

    public function test_a_user_cannot_create_a_comment_without_content()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Attach the user to the community's subscribers
        $post->community->subscribers()->attach($user->id);

        // Data to be sent with the POST request
        $data = [
            'post_id' => $post->id,
        ];
     
        // Send a POST request to the API as the user
        $response = $this->actingAs($user)->postJson($this->route, $data);
     
        // Assert that the response has status code 422
        $response->assertStatus(422);
    }

    public function test_a_user_cannot_create_a_comment_without_a_post_id()
    {
        // Create a user
        $user = User::factory()->create();

        // Data to be sent with the POST request
        $data = [
            'content' => 'This is a comment.',
        ];
     
        // Data to be sent with the POST request
        $response = $this->actingAs($user)->postJson($this->route, $data);
     
        // Assert that the response has status code 422
        $response->assertStatus(422);
    }

    public function test_a_user_cannot_create_a_comment_with_more_than_1000_characters()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Attach the user to the community's subscribers
        $post->community->subscribers()->attach($user->id);

        // Data to be sent with the POST request
        $data = [
            'content' => str_repeat('a', 1001),
            'post_id' => $post->id,
        ];

        // Send a POST request to the API as the user
        $response = $this->actingAs($user)->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);
    }

    public function test_a_user_cannot_create_a_comment_with_less_than_one_character()
    {
        // Create a user
        $user = User::factory()->create();

        // Create a post
        $post = Post::factory()->create();

        // Attach the user to the community's subscribers
        $post->community->subscribers()->attach($user->id);
        
        // Data to be sent with the POST request
        $data = [
            'content' => '',
            'post_id' => $post->id,
        ];

        // Send a POST request to the API as the user
        $response = $this->actingAs($user)->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);
    }
    
    public function test_a_user_cannot_create_a_comment_with_an_invalid_post_id()
    {
        // Create a user
        $user = User::factory()->create();
     
        // Data to be sent with the POST request
        $data = [
            'content' => 'This is a comment.',
            'post_id' => 999,
        ];

        // Send a POST request to the API as the user
        $response = $this->actingAs($user)->postJson($this->route, $data);
     
        // Assert that the response has status code 422
        $response->assertStatus(422);
    }
}
