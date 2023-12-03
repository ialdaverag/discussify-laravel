<?php

namespace Tests\Unit\User;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetUpvotedCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_upvoted_comments(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/comments/upvoted');
        $response->assertStatus(200);
    }
}
