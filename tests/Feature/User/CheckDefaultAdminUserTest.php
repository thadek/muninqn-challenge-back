<?php

namespace Tests\Feature\User;

use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckDefaultAdminUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
    }


    public function test_default_admin_user_exists(){
        $this->assertDatabaseHas('users',[
            'email' => env('ADMIN_EMAIL')
        ]);
    }

}
