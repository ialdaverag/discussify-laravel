<?php

namespace Tests\Unit;

use App\Models\User;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SignupTest extends TestCase
{
    private $route = '/api/auth/signup';

    use RefreshDatabase;
    
    /**
     * A basic unit test example.
     */
    public function test_signup_successfully(): void
    {
        $data = [
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'Password1234.'
        ];
    
        $response = $this->postJson($this->route, $data);
        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'username',
                'email',
                'created_at',
                'updated_at',
            ]);
    }

    public function test_signup_fails_without_username(): void
    {
        $invalidUserData = [
            'email' => 'test@example.com',
            'password' => 'Password1234.',
        ];

        $response = $this->postJson($this->route, $invalidUserData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_signup_fails_without_email(): void
    {
        $invalidUserData = [
            'username' => 'testuser',
            'password' => 'Password1234.',
        ];

        $response = $this->postJson($this->route, $invalidUserData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_signup_fails_without_password(): void
    {
        $invalidUserData = [
            'username' => 'testuser',
            'email' => 'test@example.com',
        ];

        $response = $this->postJson($this->route, $invalidUserData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_signup_fails_with_invalid_username(): void
    {
        $invalidUserData = [
            'username' => 'te',
            'email' => 'testuser@test.com',
            'password' => 'Password1234.',
        ];
    
        $response = $this->postJson($this->route, $invalidUserData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_signup_fails_with_invalid_email(): void
    {
        $invalidUserData = [
            'username' => 'testuser',
            'email' => 'test',
            'password' => 'Password1234.',
        ];

        $response = $this->postJson($this->route, $invalidUserData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_signup_fails_with_invalid_password(): void
    {
        $invalidUserData = [
            'username' => 'testuser',
            'email' => 'testuser@test.com',
            'password' => 'pass',
        ];
    
        $response = $this->postJson($this->route, $invalidUserData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_signup_fails_with_duplicate_username(): void
    {
        User::factory()->create(['username' => 'testuser']); 

        $duplicateUserData = [
            'username' => 'testuser',
            'email' => 'newuser@test.com',
            'password' => 'Password1234.',
        ];

        $response = $this->postJson($this->route, $duplicateUserData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_signup_fails_with_duplicate_email(): void
    {
        User::factory()->create(['email' => 'testuser@test.com']); 

        $duplicateUserData = [
            'username' => 'newuser',
            'email' => 'testuser@test.com',
            'password' => 'Password1234.',
        ];

        $response = $this->postJson($this->route, $duplicateUserData);
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
