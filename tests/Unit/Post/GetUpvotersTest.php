<?php

namespace Tests\Unit\Post;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetUpvotersTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_upvoters_successfully()
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        // Make the user upvote the post
        $user->votes()->attach($post, ['direction' => 1]);

        $response = $this->actingAs($user)->getJson("/api/post/{$post->id}/upvoters");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $user->id]);
    }

    public function test_get_upvoters_for_non_existing_post()
    {
        $user = User::factory()->create();
        $nonExistingPostId = 123456; 

        $response = $this->actingAs($user)->getJson("/api/post/{$nonExistingPostId}/upvoters");

        $response->assertStatus(404);
    }
}
