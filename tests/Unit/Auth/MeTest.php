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

    /**
     * Test me returns user details.
     *
     * @return void
     */
    public function test_me_returns_user_details(): void
    {
        // Create a password for the user
        $password = 'Password123.';

        // Create a user
        $user = User::factory()->create([
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

        // Send a GET request to /api/auth/me
        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->getJson($this->meRoute);

        // Assert that the response has status code 200
        $response->assertStatus(200);

        // Assert that the response contains correct JSON data
        $response->assertJson([
                'id' => $user->id,
                'username' => $user->username,
                'email' => $user->email,
            ]);
    }
}
