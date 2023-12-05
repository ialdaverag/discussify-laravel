<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RefreshTest extends TestCase
{
    private $loginRoute = '/api/auth/login';
    private $refreshRoute = '/api/auth/refresh';

    use RefreshDatabase;

    /**
     * Test refresh token successfully.
     *
     * @return void
     */
    public function test_refresh_token_successfully(): void
    {
        $password = 'Password123.';

        User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt($password), 
        ]);

        $data = [
            'username' => 'testuser',
            'password' => $password,
        ];

        $loginResponse = $this->postJson($this->loginRoute, $data);
        $token = $loginResponse->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson($this->refreshRoute);

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['access_token']);
    }
}
