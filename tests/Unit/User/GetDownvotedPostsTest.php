<?php

namespace Tests\Unit\User;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetDownvotedPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_downvoted_posts(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/posts/downvoted');
        $response->assertStatus(200);
    }
}
