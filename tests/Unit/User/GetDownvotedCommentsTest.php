<?php

namespace Tests\Unit;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetDownvotedCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_downvoted_comments(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/comments/downvoted');
        $response->assertStatus(200);
    }
}
