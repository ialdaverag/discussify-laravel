<?php

namespace Tests\Unit;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetUpvotedPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_upvoted_posts(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/posts/upvoted');
        $response->assertStatus(200);
    }
}
