<?php

namespace Tests\Feature\User;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CreateUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
    }

    public function test_admin_can_create_user(): void {

        $user = [
            "name" => "Gabi",
            "last_name" => "Garcia",
            "email" => "email@ejemplo.com",
            "dni"=>"12345699",
            "password" => "12345678",
            "password_confirmation" => "12345678",
            "roles" => ["admin"]
        ];

        $response = $this->useApiAs(User::find(2), 'post', "{$this->apiBase}/users",$user);



        $response->assertStatus(201);
        $response->assertJsonStructure(['data'=>['user'=>['id','email','name','last_name','roles']]]);

        $this->assertDatabaseHas('users',[
            'email' => $user['email']
        ]);

    }


    public function test_guest_cannot_create_user(): void
    {
        $user = [
            "name" => "Gabi",
            "last_name" => "Garcia",
            "email" => "guest@ejemplo.com",
            "dni" => "98765432",
            "password" => "12345678",
            "password_confirmation" => "12345678",
            "roles" => ["admin"]
        ];

        $response = $this->postJson("{$this->apiBase}/users", $user);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', [
            'email' => $user['email']
        ]);
    }


}
