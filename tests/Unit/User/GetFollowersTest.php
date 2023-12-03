<?php

namespace Tests\Unit\User;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetFollowersTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_followers(): void
    {
        $user = User::factory()->create();
        $user->followers()->attach(User::factory()->create()->id);
        $user->followers()->attach(User::factory()->create()->id);

        $response = $this->actingAs($user)->get("/api/user/{$user->username}/followers");
        $response->assertStatus(200);
        $response->assertJsonCount(2);
    }

    public function test_getting_followers_for_user_that_does_not_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/doesnotexist/followers');
        $response->assertStatus(404);
    }
}
