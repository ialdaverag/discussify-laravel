<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GetBans extends TestCase
{
    use RefreshDatabase;

    private $route = '/api/community/%s/bans';

    public function test_get_bans_successfully()
    {
        $owner = User::factory()->create();
        $community = Community::factory()->create(['user_id' => $owner->id]);

        $bans = User::factory()->count(3)->create();

        $community->bans()->attach($bans->pluck('id'));

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
