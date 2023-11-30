<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PostShowTest extends TestCase
{
    private $route = '/api/post/%s';

    use RefreshDatabase;

    /** @test */
    public function test_show_successfully()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $post = Post::factory()->create([
            'title' => 'Test Post',
            'user_id' => $user->id,
            'community_id' => $community->id,
        ]);

        $response = $this->actingAs($user)
                         ->getJson(sprintf($this->route, $post->id));

        $response
            ->assertStatus(200);
    }

    /** @test */
    public function test_post_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
                         ->getJson(sprintf($this->route, 1));

        $response->assertStatus(404);
    }
}
