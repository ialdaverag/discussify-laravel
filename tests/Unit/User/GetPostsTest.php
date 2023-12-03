<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_posts(): void
    {
        $user = User::factory()->create();
        $community = Community::factory()->create(); // Create a community

        $user->posts()->create([
            'title' => 'Test post',
            'content' => 'Test post body',
            'community_id' => $community->id, // Associate the post with the community
        ]);

        $response = $this->actingAs($user)->get("/api/user/{$user->username}/posts");
        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_getting_posts_for_user_that_does_not_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/doesnotexist/posts');
        $response->assertStatus(404);
    }
}
