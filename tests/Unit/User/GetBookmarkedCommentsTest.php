<?php

namespace Tests\Unit;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetBookmarkedCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_bookmarked_comments(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/comments/bookmarked');
        $response->assertStatus(200);
    }
}
