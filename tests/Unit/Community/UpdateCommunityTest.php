<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateCommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_update_successfully(): void
    {
        $community = Community::factory()->create();
        $owner = $community->user;

        $response = $this->actingAs($owner)->patchJson("/api/community/{$community->name}", [
            'name' => 'UpdatedName',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('UpdatedName', $community->fresh()->name);
    }

    public function test_update_fails_for_non_owner(): void
    {
        $community = Community::factory()->create();
        $nonOwner = User::factory()->create();

        $response = $this->actingAs($nonOwner)->patchJson("/api/community/{$community->name}", [
            'name' => 'UpdatedName',
        ]);

        $response->assertStatus(403);
        $this->assertEquals($community->name, $community->fresh()->name);
    }

    public function test_update_fails_for_non_existent_community(): void
    {
        $user = User::factory()->create();
        $nonExistentCommunityName = 'non-existent-community';

        $response = $this->actingAs($user)->patchJson("/api/community/{$nonExistentCommunityName}", [
            'name' => 'Updated Name',
        ]);

        $response->assertStatus(404);
    }

    public function test_update_fails_for_duplicate_name(): void
    {
        $community = Community::factory()->create();
        $owner = $community->user;
        $otherCommunity = Community::factory()->create();

        $response = $this->actingAs($owner)->patchJson("/api/community/{$community->name}", [
            'name' => $otherCommunity->name,
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }
}
