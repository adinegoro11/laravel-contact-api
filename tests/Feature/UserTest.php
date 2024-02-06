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

    public function test_current_user_success(): void
    {
        $this->seed([UserSeeder::class]);
        $response = $this->get('/api/users/current', [
            'Authorization' => 'test-token',
        ]);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'username' => 'test',
                'email' => 'test@test.com',
            ],
        ]);
    }

    public function test_current_user_unauthorized(): void
    {
        $this->seed([UserSeeder::class]);
        $response = $this->get('/api/users/current');
        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [
                'message' =>  [
                    'unauthorized'
                ]
            ],
        ]);
    }

    public function test_current_invalid_token(): void
    {
        $this->seed([UserSeeder::class]);
        $response = $this->get('/api/users/current', [
            'Authorization' => 'invalid-token',
        ]);
        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [
                'message' =>  [
                    'unauthorized'
                ]
            ],
        ]);
    }

    public function test_success_update_name(): void
    {
        $this->seed([UserSeeder::class]);
        $new_username = 'Ricardo';
        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->patch('/api/users/current', ['username' => $new_username]);
        $response->assertStatus(200);
        $response->assertJsonPath('data.username', fn (string $check) => ($check) == $new_username);
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseHas('users', [
            'username' => $new_username,
        ]);
    }

    public function test_failed_update_name(): void
    {
        $this->seed([UserSeeder::class]);
        $new_username = 'da';
        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->patch('/api/users/current', ['username' => $new_username]);
        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'username' => [
                    'The username field must be at least 3 characters.'
                ],
            ]
        ]);
    }

    public function test_success_logout(): void
    {
        $this->seed([UserSeeder::class]);
        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->delete('/api/users/logout', []);
        $response->assertStatus(200);
        $response->assertJson([
            'data' => true,
        ]);

        $this->assertDatabaseHas('users', [
            'username' => 'test',
            'token' => null,
        ]);
    }

    public function test_failed_logout(): void
    {
        $this->seed([UserSeeder::class]);
        $response = $this->withHeaders([
            'Authorization' => 'invalid-token',
            'Accept' => 'application/json'
        ])->delete('/api/users/logout', []);
        $response->assertStatus(401);
        $response->assertJson([
            'errors' => [
                'message' => [
                    'unauthorized'
                ],
            ]
        ]);
    }
}
