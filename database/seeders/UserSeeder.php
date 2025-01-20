<?php

namespace Database\Seeders;

use App\Enums\Roles;
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
        $user =User::factory()->create([
            'name' => 'Test ',
            'email' => 'example@example.com',
            'last_name' => 'User',
            'dni' => '12345678',

        ]);
        $user->assignRole(Roles::USER->value);



        $admin =User::factory()->create([
            'name' => 'Admin ',
            'email' => env('ADMIN_EMAIL','admin@example.com'),
            'password' => Hash::make(env('ADMIN_PASSWORD','password')),
            'last_name' => 'User',
            'dni' => '11223344',
        ]);
        $admin->assignRole(Roles::ADMIN->value);
    }
}
