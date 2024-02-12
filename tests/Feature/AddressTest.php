<?php

namespace Tests\Feature;

use App\Models\Contact;
use Database\Seeders\ContactSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    public function test_create_success(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $params = [
            'street' => 'Kebon Baru',
            'city' => 'Tebet',
            'province' => 'DKI Jakarta',
            'country' => 'Indonesia',
            'postal_code' => 12830,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->post('/api/contacts/' . $contact->id . '/addresses', $params);

        $response->assertStatus(201);
        $response->assertJsonPath('data.street', fn (string $street) => ($street) == $params['street']);
        $response->assertJsonPath('data.city', fn (string $city) => ($city) == $params['city']);
        $response->assertJsonPath('data.province', fn (string $province) => ($province) == $params['province']);
        $response->assertJsonPath('data.country', fn (string $country) => ($country) == $params['country']);
        $response->assertJsonPath('data.postal_code', fn (string $postal_code) => ($postal_code) == $params['postal_code']);
    }

    public function test_create_failed(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $params = [
            'street' => 'Kebon Baru',
            'city' => 'Tebet',
            'province' => 'DKI Jakarta',
            'country' => '',
            'postal_code' => 12830,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->post('/api/contacts/' . $contact->id . '/addresses', $params);

        $response->assertStatus(400);
        $response->assertJson([
            'errors' => [
                'country' => [
                    'The country field is required.'
                ]
            ]
        ]);
    }

    public function test_create_contact_not_found(): void
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $params = [
            'street' => 'Kebon Baru',
            'city' => 'Tebet',
            'province' => 'DKI Jakarta',
            'country' => 'Indonesia',
            'postal_code' => 12830,
        ];

        $response = $this->withHeaders([
            'Authorization' => 'test-token',
            'Accept' => 'application/json'
        ])->post('/api/contacts/' . ($contact->id + 3) . '/addresses', $params);

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
