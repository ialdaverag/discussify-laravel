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
        $password = 'Password123.';

        User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt($password), 
        ]);

        $data = [
            'username' => 'testuser',
            'password' => $password,
        ];

        $response = $this->postJson($this->route, $data);
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['access_token']);
    }

    /**
     * Test login fails without username.
     *
     * @return void
     */
    public function test_login_fails_without_username(): void
    {
        $data = [
            'password' => 'Password123.',
        ];

        $response = $this->postJson($this->route, $data);
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
        $data = [
            'username' => 'testuser',
        ];

        $response = $this->postJson($this->route, $data);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }

    /**
     * Test login fails with invalid username.
     *
     * @return void
     */
    public function test_login_with_invalid_username(): void
    {
        $password = 'Password123.';

        User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt($password), 
        ]);

        $invalidLoginData = [
            'username' => 'wrongusername', // incorrect username
            'password' => $password,
        ];

        $response = $this->postJson($this->route, $invalidLoginData);
        $response->assertStatus(401);
    }

    /**
     * Test login fails with invalid password.
     *
     * @return void
     */
    public function test_login_with_invalid_password(): void
    {
        $password = 'Password123.';

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