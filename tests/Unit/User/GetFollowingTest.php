<?php

namespace Tests\Unit\User;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetFollowingTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_following(): void
    {
        $user = User::factory()->create();
        $user->following()->attach(User::factory()->create()->id);
        $user->following()->attach(User::factory()->create()->id);

        $response = $this->actingAs($user)->get("/api/user/{$user->username}/following");
        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_getting_following_for_user_that_does_not_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/doesnotexist/following');
        $response->assertStatus(404);
    }
}
