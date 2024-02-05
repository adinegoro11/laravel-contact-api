<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_success_create(): void
    {
        $this->seed([UserSeeder::class]);
        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->post('/api/contacts', [
            'first_name' => 'Sergio',
            'last_name' => 'Ramos',
            'phone' => '08388222',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'data' => [
                'first_name' => 'Sergio',
                'last_name' => 'Ramos',
                'phone' => '08388222',
            ]
        ]);
    }
}
