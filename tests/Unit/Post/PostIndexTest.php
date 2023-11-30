<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostIndexTest extends TestCase
{
    private $route = '/api/post';

    use RefreshDatabase;

    /** @test */
    public function test_index_successfully()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $post = Post::factory()->create([
            'title' => 'Test Post',
            'user_id' => $user->id,
            'community_id' => $community->id,
        ]);

        $response = $this->actingAs($user)
                         ->getJson($this->route);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'content',
                        'owner',
                        'community',
                        'created_at',
                        'updated_at',
                    ]
                ]
            ]);
    }
}
