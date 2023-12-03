<?php

namespace Tests\Unit\User;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_following_a_user(): void
    {
        $user = User::factory()->create();
        $userToFollow = User::factory()->create();

        $response = $this->actingAs($user)->post("/api/user/{$userToFollow->username}/follow");
        $response->assertStatus(204);

        $this->assertDatabaseHas('follows', [
            'follower_id' => $user->id,
            'followed_id' => $userToFollow->id,
        ]);
    }

    public function test_following_a_user_that_does_not_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/user/doesnotexist/follow');
        $response->assertStatus(404);
    }

    public function test_following_a_user_that_is_already_followed(): void
    {
        $user = User::factory()->create();
        $userToFollow = User::factory()->create();

        $user->followers()->attach($userToFollow->id);

        $response = $this->actingAs($user)->post("/api/user/{$userToFollow->username}/follow");
        $response->assertStatus(204);

        $this->assertDatabaseHas('follows', [
            'follower_id' => $user->id,
            'followed_id' => $userToFollow->id,
        ]);
    }

    public function test_following_a_user_that_is_yourself(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post("/api/user/{$user->username}/follow");
        $response->assertStatus(422);
    }
}
