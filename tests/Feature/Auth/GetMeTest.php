<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetMeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
    }

    public function test_get_me_endpoint_returns_user_data():void{

        # Haciendo
        $response = $this->useApiAs(User::find(1), 'GET', "{$this->apiBase}/auth/me");

        # Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data'=>['id','name','last_name','email','roles']]);
    }
}
