<?php

namespace Tests\Unit;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UnfollowTest extends TestCase
{
    use RefreshDatabase;

    public function test_unfollowing_a_user(): void
    {
        $user = User::factory()->create();
        $userToUnfollow = User::factory()->create();

        $user->followers()->attach($userToUnfollow->id);

        $response = $this->actingAs($user)->post("/api/user/{$userToUnfollow->username}/unfollow");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $user->id,
            'followed_id' => $userToUnfollow->id,
        ]);
    }

    public function test_unfollowing_a_user_that_does_not_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/api/user/doesnotexist/unfollow');
        $response->assertStatus(404);
    }

    public function test_unfollowing_a_user_that_is_not_followed(): void
    {
        $user = User::factory()->create();
        $userToUnfollow = User::factory()->create();

        $response = $this->actingAs($user)->post("/api/user/{$userToUnfollow->username}/unfollow");
        $response->assertStatus(204);

        $this->assertDatabaseMissing('follows', [
            'follower_id' => $user->id,
            'followed_id' => $userToUnfollow->id,
        ]);
    }

    public function test_unfollowing_a_user_that_is_yourself(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post("/api/user/{$user->username}/unfollow");
        $response->assertStatus(422);
    }
}
