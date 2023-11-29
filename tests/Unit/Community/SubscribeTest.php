<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscribeTest extends TestCase
{
    private $route = '/api/community/%s/subscribe';

    use RefreshDatabase;

    public function test_user_can_subscribe_to_community(): void
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();

        $response = $this->actingAs($user)->postJson(sprintf($this->route, $community->name));

        $response->assertStatus(204);
    }

    public function test_user_cannot_subscribe_to_same_community_twice(): void
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();

        $this->actingAs($user)->postJson(sprintf($this->route, $community->name));

        $response = $this->actingAs($user)->postJson(sprintf($this->route, $community->name));

        $response
            ->assertStatus(400)
            ->assertJson(['error' => 'User is already subscribed to the community']);
    }

    public function test_user_cannot_subscribe_to_nonexistent_community(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(sprintf($this->route, 'nonexistent'));

        $response->assertStatus(404);
    }
}
