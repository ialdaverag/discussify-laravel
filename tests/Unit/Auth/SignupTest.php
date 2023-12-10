<?php

namespace Tests\Unit\Auth;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SignupTest extends TestCase
{
    private $route = '/api/auth/signup';

    use RefreshDatabase;
    
    /**
     * Test signup successfully.
     *
     * @return void
     */
    public function test_signup_successfully(): void
    {
        // Data to be sent in the request
        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password123.'
        ];
    
        // Send a POST request to /api/auth/signup
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 201
        $response->assertStatus(201);

        // Assert that the response contains correct JSON data
        $response->assertJsonStructure([
                'id',
                'username',
                'email',
                'created_at',
                'updated_at',
            ]);
    }

    /**
     * Test signup fails without username.
     *
     * @return void
     */
    public function test_signup_fails_without_username(): void
    {
        // Data to be sent in the request
        $data = [
            'email' => 'test@example.com',
            'password' => 'Password123.',
        ];

        // Send a POST request to /api/auth/signup
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response has correct JSON data
        $response->assertJsonValidationErrors(['username']);
    }

    /**
     * Test signup fails without email.
     *
     * @return void
     */
    public function test_signup_fails_without_email(): void
    {
        // Data to be sent in the request
        $data = [
            'username' => 'testuser',
            'password' => 'Password123.',
        ];

        // Send a POST request to /api/auth/signup
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response has correct JSON data
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test signup fails without password.
     *
     * @return void
     */
    public function test_signup_fails_without_password(): void
    {
        // Data to be sent in the request
        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ];

        // Send a POST request to /api/auth/signup
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response has correct JSON data
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * Test signup fails with invalid username.
     *
     * @return void
     */
    public function test_signup_fails_with_invalid_username(): void
    {
        // Data to be sent in the request
        $data = [
            'username' => 'te',
            'email' => 'testuser@test.com',
            'password' => 'Password123.',
        ];
    
        // Send a POST request to /api/auth/signup
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response has correct JSON data
        $response->assertJsonValidationErrors(['username']);
    }

    /**
     * Test signup fails with invalid email.
     *
     * @return void
     */
    public function test_signup_fails_with_invalid_email(): void
    {
        // Data to be sent in the request
        $data = [
            'username' => 'testuser',
            'email' => 'test',
            'password' => 'Password1234.',
        ];

        // Send a POST request to /api/auth/signup
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response has correct JSON data
        $response->assertJsonValidationErrors(['email']);
    }

    /**
     * Test signup fails with invalid password.
     *
     * @return void
     */
    public function test_signup_fails_with_invalid_password(): void
    {
        // Data to be sent in the request
        $data = [
            'username' => 'testuser',
            'email' => 'testuser@test.com',
            'password' => 'pass',
        ];
    
        // Send a POST request to /api/auth/signup
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response has correct JSON data
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * Test signup fails with password that does not contain a number.
     *
     * @return void
     */
    public function test_signup_fails_with_duplicate_username(): void
    {
        // Create a user
        User::factory()->create(['username' => 'testuser']); 

        // Data to be sent in the request
        $data = [
            'username' => 'testuser',
            'email' => 'newuser@test.com',
            'password' => 'Password123.',
        ];

        // Send a POST request to /api/auth/signup
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response has correct JSON data
        $response->assertJsonValidationErrors(['username']);
    }

    /**
     * Test signup fails with duplicate email.
     *
     * @return void
     */
    public function test_signup_fails_with_duplicate_email(): void
    {
        // Create a user
        User::factory()->create(['email' => 'testuser@test.com']); 

        // Data to be sent in the request
        $data = [
            'username' => 'newuser',
            'email' => 'testuser@test.com',
            'password' => 'Password123.',
        ];

        // Send a POST request to /api/auth/signup
        $response = $this->postJson($this->route, $data);

        // Assert that the response has status code 422
        $response->assertStatus(422);

        // Assert that the response has correct JSON data
        $response->assertJsonValidationErrors(['email']);
    }
}
