<?php

namespace Tests\Feature\User;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EditUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
    }


    public function test_admin_can_edit_user_with_put_method(): void {

        $user = [
            "name" => "Gabi",
            "last_name" => "Garcia",
            "email" => "email@ejemplo.com",
            "dni"=>"12345699",
            "password" => "12345678",
            "password_confirmation" => "12345678",
            "roles" => ["admin","user"]
        ];

        $response = $this->useApiAs(User::find(2), 'put', "{$this->apiBase}/users/1",$user);


        $response->assertStatus(200);
        $response->assertJsonStructure(['data'=>['user'=>['id','email','name','last_name','roles']]]);
        $this->assertDatabaseHas('users',[
            'email' => $user['email'],
            'name' => 'Gabi',
            'last_name' => 'Garcia'
        ]);
    }


    public function test_admin_can_edit_user_with_patch_method(): void {
        $user = [
            "name" => "Gabi",
            "last_name" => "Garcia",
        ];

        $response = $this->useApiAs(User::find(2), 'patch', "{$this->apiBase}/users/1",$user);


        $response->assertStatus(200);
        $response->assertJsonStructure(['data'=>['user'=>['id','email','name','last_name','roles']]]);
        $this->assertDatabaseHas('users',[
            'id'=> 1,
            'name' => 'Gabi',
            'last_name' => 'Garcia'
        ]);

    }


    public function test_admin_can_edit_user_roles_with_patch_method(): void {
        $user = [
            "roles" => ["admin","user"]
        ];

        $response = $this->useApiAs(User::find(2), 'patch', "{$this->apiBase}/users/1",$user);


        $response->assertStatus(200);
        $response->assertJsonStructure(['data'=>['user'=>['id','email','name','last_name','roles']]]);
        $this->assertDatabaseHas('model_has_roles',[
            'role_id'=> 1,
            'model_id'=> 1,
        ]);
        $this->assertDatabaseHas('model_has_roles',[
            'role_id'=> 2,
            'model_id'=> 1,
        ]);

    }


    //TODO: tests validaciones




}
