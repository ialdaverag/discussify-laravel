<?php

namespace Tests\Unit;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetCommentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_comments(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get("/api/user/{$user->username}/comments");
        $response->assertStatus(200);
    }

    public function test_getting_comments_for_user_that_does_not_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/doesnotexist/comments');
        $response->assertStatus(404);
    }
}
