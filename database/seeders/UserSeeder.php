<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'test',
            'password' => Hash::make('test'),
            'email' => 'test@test.com',
            'token' => 'test-token',
        ]);

        User::create([
            'username' => 'second_user',
            'password' => Hash::make('test'),
            'email' => 'second@user.com',
            'token' => 'second-token',
        ]);
    }
}
