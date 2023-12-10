<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private $route = '/api/auth/login';

    /**
     * Test login successfully.
     *
     * @return void
     */
    public function test_login_successfully(): void
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
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 200
        $response->assertStatus(200);

        // Assert that the response contains correct JSON data
        $response->assertJsonStructure(['access_token']);
    }

    /**
     * Test login fails without username.
     *
     * @return void
     */
    public function test_login_fails_without_username(): void
    {
        // Data to be sent in the request
        $data = [
            'password' => 'Password123.',
        ];

        // Send a POST request to /api/auth/login
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    /**
     * Test login fails without password.
     *
     * @return void
     */
    public function test_login_fails_without_password(): void
    {
        // Data to be sent in the request
        $data = [
            'username' => 'testuser',
        ];

        // Send a POST request to /api/auth/login
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response has correct JSON data
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * Test login fails with invalid username.
     *
     * @return void
     */
    public function test_login_with_invalid_username(): void
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
            'username' => 'wrongusername', // incorrect username
            'password' => $password,
        ];

        // Send a POST request to /api/auth/login
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 401
        $response->assertStatus(401);
    }

    /**
     * Test login fails with invalid password.
     *
     * @return void
     */
    public function test_login_with_invalid_password(): void
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
            'password' => 'wrongpassword', // incorrect password
        ];

        // Send a POST request to /api/auth/login
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 401
        $response->assertStatus(401);
    }
}
?>