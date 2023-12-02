<?php

namespace Tests\Unit\Community;

use App\Models\User;
use App\Models\Community;
use App\Models\Post;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_posts_successfully()
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $post = Post::factory()->create();

        $community->posts()->save($post);

        $response = $this->actingAs($user)->getJson("/api/community/{$community->name}/posts");

        $response->assertStatus(200);
    }

    public function test_get_posts_for_non_existing_community()
    {
        $user = User::factory()->create();
        $nonExistingCommunityName = 'non-existing-community';

        $response = $this->actingAs($user)->getJson("/api/community/{$nonExistingCommunityName}/posts");

        $response->assertStatus(404);
    }
}
