<?php

namespace Tests\Feature\User;

use App\Enums\Roles;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetUsersTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
    }


    public function test_get_users_endpoint_returns_users_data():void{

        #Haciendo un get a users
        $response = $this->useApiAs(User::find(2),'get',"{$this->apiBase}/users");

        // Esperando respuesta
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'last_name',
                        'email',
                        'dni',
                        'roles'
                    ]
                ]
            ]);
    }



    public function test_get_users_endpoint_returns_search_param_users_data():void{


        #Haciendo un get a users
        $response = $this->useApiAs(User::find(2),'get',"{$this->apiBase}/users?search=test",);

        // Esperando respuesta
        $response->assertStatus(200);


        $response->assertJsonCount(1, 'data');

    }


    public function test_get_users_endpoint_cant_be_accessed_by_users_without_role_admin():void{

        $response = $this->useApiAs(User::factory()->create(), 'get', "{$this->apiBase}/users");

        $response->assertStatus(403);

    }


    public function test_get_single_user_endpoint_returns_single_user_data(): void
    {
        // Haciendo un get a users/1
        $response = $this->useApiAs(User::find(2), 'get', "{$this->apiBase}/users/1");

        // Esperando respuesta
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'last_name',
                    'email',
                    'dni',
                    'roles'
                ]
            ]);


        // Confirmar que solo se retorna un usuario
        $response->assertJsonFragment(['id' => 1,'name'=>"Test ","last_name"=>"User"]);
    }




}
