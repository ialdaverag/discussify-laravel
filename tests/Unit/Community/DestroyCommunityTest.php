<?php

namespace Tests\Unit\Community;

use App\Models\Community;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DestroyCommunityTest extends TestCase
{
    use RefreshDatabase;

    public function test_destroy_successfully(): void
    {
        $community = Community::factory()->create();
        $owner = $community->user;

        $response = $this->actingAs($owner)->deleteJson("/api/community/{$community->name}");

        $response->assertStatus(204);
        $this->assertNull($community->fresh());
    }

    public function test_destroy_fails_for_non_owner(): void
    {
        $community = Community::factory()->create();
        $nonOwner = User::factory()->create();

        $response = $this->actingAs($nonOwner)->deleteJson("/api/community/{$community->name}");

        $response->assertStatus(403);
        $this->assertNotNull($community->fresh());
    }

    public function test_destroy_fails_for_non_existent_community(): void
    {
        $user = User::factory()->create();
        $nonExistentCommunityName = 'non-existent-community';

        $response = $this->actingAs($user)->deleteJson("/api/community/{$nonExistentCommunityName}");

        $response->assertStatus(404);
    }
}
