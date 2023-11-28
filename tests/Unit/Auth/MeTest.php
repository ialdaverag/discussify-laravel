<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class MeTest extends TestCase
{
    private $loginRoute = '/api/auth/login';
    private $meRoute = '/api/auth/me';
    
    use RefreshDatabase;

    public function test_me_returns_user_details(): void
    {
        $password = 'Password1234.';
        $user = User::factory()->create([
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
        ])->getJson($this->meRoute);
        $response
            ->assertStatus(200)
            ->assertJson([
                'username' => $user->username,
            ]);
    }
}
