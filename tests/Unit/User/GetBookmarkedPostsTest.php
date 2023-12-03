<?php

namespace Tests\Unit\User;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetBookmarkedPostsTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_bookmarked_posts(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/posts/bookmarked');
        $response->assertStatus(200);
    }
}
