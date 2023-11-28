<?php

namespace Tests\Unit\User;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserIndexTest extends TestCase
{
    private $route = '/api/user';

    use RefreshDatabase;

    public function test_index_returns_all_users(): void
    {
        $users = User::factory()->count(3)->create();

        $response = $this->get($this->route);
        $response
            ->assertStatus(200)
            ->assertJson($users->toArray());
    }
}
