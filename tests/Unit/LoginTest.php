<?php

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private $route = '/api/auth/login';

    public function test_login_successfully(): void
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

        $response = $this->postJson($this->route, $loginData);
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['access_token']);
    }

    public function test_login_fails_without_username(): void
    {
        $loginDataWithoutUsername = [
            'password' => 'Password1234.',
        ];

        $response = $this->postJson($this->route, $loginDataWithoutUsername);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['username']);
    }

    public function test_login_fails_without_password(): void
    {
        $loginDataWithoutPassword = [
            'username' => 'testuser',
        ];

        $response = $this->postJson($this->route, $loginDataWithoutPassword);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    public function test_login_with_invalid_credentials(): void
    {
        $password = 'Password1234.';
        User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt($password), 
        ]);

        $invalidLoginData = [
            'username' => 'testuser',
            'password' => 'wrongpassword', // incorrect password
        ];

        $response = $this->postJson($this->route, $invalidLoginData);
        $response->assertStatus(401);
    }
}
?>