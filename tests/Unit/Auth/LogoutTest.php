<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    use RefreshDatabase;

    private $loginRoute = '/api/auth/login';
    private $logoutRoute = '/api/auth/logout';

    public function test_logout_successfully(): void
    {
        $password = 'Password1234.';
        User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt($password), 
        ]);

        $loginData = [
            'username' => 'testuser',
            'password' => $password,
        ];

        $loginResponse = $this->postJson($this->loginRoute, $loginData);
        $token = $loginResponse->json('access_token');

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson($this->logoutRoute);

        $response->assertStatus(200);
    }
}
