<?php

namespace Tests\Unit\Post;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostStoreTest extends TestCase
{
    private $route = '/api/post';

    use RefreshDatabase;

    /** @test */
    public function test_store_successfully()
    {
        $user = User::factory()->create();
        $postData = Post::factory()->make()->toArray();

        $response = $this->actingAs($user)
                         ->postJson($this->route, $postData);

        $response
            ->assertStatus(201)
            ->assertJsonStructure([
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
    public function test_store_without_title()
    {
        $user = User::factory()->create();
        $postData = Post::factory()->make()->toArray();
        unset($postData['title']);

        $response = $this->actingAs($user)
                         ->postJson($this->route, $postData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    /** @test */
    public function test_store_without_content()
    {
        $user = User::factory()->create();
        $postData = Post::factory()->make()->toArray();
        unset($postData['content']);

        $response = $this->actingAs($user)
                         ->postJson($this->route, $postData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('content');
    }

    /** @test */
    public function test_store_without_community_id()
    {
        $user = User::factory()->create();
        $postData = Post::factory()->make()->toArray();
        unset($postData['community_id']);

        $response = $this->actingAs($user)
                         ->postJson($this->route, $postData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('community_id');
    }

    /** @test */
    public function test_store_with_invalid_community_id()
    {
        $user = User::factory()->create();
        $postData = Post::factory()->make()->toArray();
        $postData['community_id'] = 999;

        $response = $this->actingAs($user)
                         ->postJson($this->route, $postData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('community_id');
    }

    /** @test */
    public function test_store_with_invalid_title()
    {
        $user = User::factory()->create();
        $postData = Post::factory()->make()->toArray();
        $postData['title'] = 'a';

        $response = $this->actingAs($user)
                         ->postJson($this->route, $postData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    /** @test */
    public function test_store_with_invalid_content()
    {
        $user = User::factory()->create();
        $postData = Post::factory()->make()->toArray();
        $postData['content'] = '';

        $response = $this->actingAs($user)
                         ->postJson($this->route, $postData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('content');
    }
}
