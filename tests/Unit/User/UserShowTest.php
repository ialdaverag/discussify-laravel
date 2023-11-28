<?php

namespace Tests\Unit\User;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserShowTest extends TestCase
{
    private $showRouteTemplate = '/api/user/%s';

    use RefreshDatabase;

    public function test_show_returns_the_specified_user(): void
    {
        $user = User::factory()->create();

        $response = $this->get(sprintf($this->showRouteTemplate, $user->username));

        $response
            ->assertStatus(200)
            ->assertJson($user->toArray());
    }

    public function test_show_returns_not_found_for_invalid_username(): void
    {
        $invalidUsername = 'nonexistentUser';

        $response = $this->get(sprintf($this->showRouteTemplate, $invalidUsername));

        $response->assertStatus(404);
    }
}
