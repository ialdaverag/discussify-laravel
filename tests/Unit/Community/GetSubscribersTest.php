<?php

namespace Tests\Unit;

use App\Models\Community;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetSubscribersTest extends TestCase
{
    use RefreshDatabase;

    private $route = '/api/community/%s/subscribers';

    public function test_get_subscribers_successfully()
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);

        $subscribers = User::factory()->count(3)->create();

        $community->subscribers()->attach($subscribers->pluck('id'));

        $response = $this->actingAs($owner)->getJson(sprintf($this->route, $community->name));
        $response->assertStatus(200);
    }

    public function test_community_not_found()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson(sprintf($this->route, 'nonexistent_community'));
        $response->assertStatus(404);
    }
}
