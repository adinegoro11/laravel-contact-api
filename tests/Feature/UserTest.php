<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_register_success(): void
    {
        $response = $this->post('/api/users', [
            'username' => 'angga',
            'password' => 'angga',
            'email' => 'angga@angga.com',
        ]);
        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'username' => 'angga',
                'email' => 'angga@angga.com',
            ]
        ]);
    }

    public function test_register_failed(): void
    {
        $response = $this->post('/api/users', [
            'username' => null,
            'password' => '',
            'email' => 'angga@angga.com',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'username' => [
                    'The username field is required.'
                ],
                'password' => [
                    'The password field is required.'
                ],
            ]
        ]);
    }

    public function test_username_already_exists(): void
    {
        $this->test_register_success();
        $response = $this->post('/api/users', [
            'username' => 'angga',
            'password' => 'angga',
            'email' => 'angga@angga.com',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'username' => [
                    'username already registered'
                ],
            ]
        ]);
    }

    public function test_login_success(): void
    {
        $this->seed([UserSeeder::class]);
        $response = $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test',
        ]);
        $response->assertStatus(200);
        $response->assertJsonPath('data.username', fn (string $username) => ($username) == 'test');
        $response->assertJsonPath('data.email', fn (string $email) => ($email) == 'test@test.com');
        $user = User::where('username', 'test')->first();
        $this->assertNotNull($user->token);
    }

    public function test_login_failed_username_not_found(): void
    {
        $response = $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test',
        ]);
        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [
                'message' => [
                    'Invalid username password'
                ],
            ]
        ]);
    }

    public function test_login_failed_wrong_password(): void
    {
        $this->seed([UserSeeder::class]);
        $response = $this->post('/api/users/login', [
            'username' => 'test',
            'password' => '1234',
        ]);
        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [
                'message' => [
                    'Invalid username password'
                ],
            ]
        ]);
    }
}
