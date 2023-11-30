<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnsubscribeTest extends TestCase
{
    private $subscribeRoute = '/api/community/%s/subscribe';
    private $unsubscribeRoute = '/api/community/%s/unsubscribe';
    private $community;

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->community = Community::factory()->create();
        $this->unsubscribeRoute = sprintf($this->unsubscribeRoute, $this->community->name);
    }

    public function test_user_can_unsubscribe_from_community(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(sprintf($this->subscribeRoute, $this->community->name));
        $this->assertTrue($user->fresh()->isSubscribedTo($this->community));

        $response = $this->actingAs($user)->postJson($this->unsubscribeRoute);

        $response->assertStatus(204);
        $this->assertFalse($user->fresh()->isSubscribedTo($this->community));
    }

    public function test_user_cannot_unsubscribe_from_community_not_subscribed_to(): void
    {
        $user = User::factory()->create();
        $community = Community::factory()->create();

        $response = $this->actingAs($user)->postJson(sprintf($this->unsubscribeRoute, $community->name));

        $response->assertStatus(400);
    }

    // public function test_user_cannot_unsubscribe_to_nonexistent_community(): void
    // {
    //     $user = User::factory()->create();

    //     $response = $this->actingAs($user)->postJson(sprintf($this->unsubscribeRoute, 'nonexistent'));

    //     $response->assertStatus(404);
    // }
}
