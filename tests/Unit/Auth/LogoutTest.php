<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LogoutTest extends TestCase
{
    private $loginRoute = '/api/auth/login';
    private $logoutRoute = '/api/auth/logout';

    use RefreshDatabase;

    /**
     * Test logout successfully.
     *
     * @return void
     */
    public function test_logout_successfully(): void
    {
        // Create a password for the user
        $password = 'Password123.';

        // Create a user
        User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt($password), 
        ]);

        // Data to be sent in the request
        $data = [
            'username' => 'testuser',
            'password' => $password,
        ];

        // Send a POST request to /api/auth/login
        $loginResponse = $this->postJson($this->loginRoute, $data);

        // Assert that the response has status code 200
        $token = $loginResponse->json('access_token');

        // Send a POST request to /api/auth/logout
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->postJson($this->logoutRoute);

        // Assert that the response has status code 200
        $response->assertStatus(200);

        // Assert that the response contains correct JSON data
        $response->assertJson([
                'message' => 'Successfully logged out.',
            ]);
    }
}
