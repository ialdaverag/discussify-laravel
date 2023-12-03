<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Community;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetSubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_getting_subscriptions(): void
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();
        $community->subscribers()->attach($user->id);

        $response = $this->actingAs($user)->get("/api/user/{$user->username}/subscriptions");
        $response->assertStatus(200);
        $response->assertJsonCount(1);
    }

    public function test_getting_subscriptions_for_user_that_does_not_exist(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/api/user/doesnotexist/subscriptions');
        $response->assertStatus(404);
    }
}
