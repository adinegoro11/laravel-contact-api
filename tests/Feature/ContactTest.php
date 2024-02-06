<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ContactTest extends TestCase
{
    public function test_create_success(): void
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

    public function test_create_failed(): void
    {
        $this->seed([UserSeeder::class]);
        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Ramos',
            'phone' => '08388222',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.'
                ]
            ]
        ]);
    }

    public function test_get_contact_success(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->get('/api/contacts/' . $contact->id, []);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => [
                'first_name' => 'Cristiano',
                'last_name' => 'Ronaldo',
                'phone' => '11111111',
            ]
        ]);
    }

    public function test_get_contact_not_found(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->get('/api/contacts/' . ($contact->id + 2), []);

        $response->assertStatus(404);
        $response->assertJson([
            'errors' => [
                'message' => [
                    'not found'
                ]
            ]
        ]);
    }

    public function test_get_another_contact(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);

        $contact = Contact::query()->limit(1)->first();

        $response = $this->withHeaders([
            'Authorization' => 'second-token',
            'Accept' => 'application/json'
        ])->get('/api/contacts/' . $contact->id, []);

        $response->assertStatus(404);
        $response->assertJson([
            'errors' => [
                'message' => [
                    'not found'
                ]
            ]
        ]);
    }

    public function test_update_success(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->put('/api/contacts/' . $contact->id, [
            'first_name' => 'Jamie',
            'last_name' => 'Vardy',
            'phone' => '222222',
        ]);

        $response->assertStatus(200);
        $response->assertJsonPath('data.first_name', fn (string $check) => ($check) == 'Jamie');
        $response->assertJsonPath('data.last_name', fn (string $check) => ($check) == 'Vardy');
        $response->assertJsonPath('data.phone', fn (string $check) => ($check) == '222222');
    }

    public function test_update_with_empty_firstname(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->put('/api/contacts/' . $contact->id, [
            'first_name' => '',
            'last_name' => 'Vardy',
            'phone' => '222222',
        ]);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'first_name' => [
                    'The first name field is required.'
                ]
            ]
        ]);
    }

    public function test_delete_success(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->delete('/api/contacts/' . $contact->id, []);

        $response->assertStatus(200);
        $response->assertJson([
            'data' => 'success'
        ]);
    }

    public function test_delete_not_found(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->delete('/api/contacts/' . ($contact->id + 2), []);

        $response->assertStatus(404);
        $response->assertJson([
            'errors' => [
                'message' => [
                    'not found'
                ]
            ]
        ]);
    }
}
