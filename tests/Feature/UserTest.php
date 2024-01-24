<?php

namespace Tests\Feature;

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
        // $response->dump();
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
}
