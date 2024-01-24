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

    // public function test_register_failed(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }

    // public function test_username_already_exists(): void
    // {
    //     $response = $this->get('/');

    //     $response->assertStatus(200);
    // }
}
