<?php

namespace Tests\Feature\User;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DeleteUserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
    }


    public function test_unauthenticated_user_cannot_delete_user(): void
    {
        $userId = 1;

        $response = $this->deleteJson(route('users.destroy', $userId));

        $response->assertStatus(403);
    }


    public function test_normal_user_cannot_delete_user(): void
    {

        $this->actingAs(User::find(1));

        $userId = 1;

        $response = $this->deleteJson(route('users.destroy', $userId));

        $response->assertStatus(403);

        $this->assertDatabaseHas('users',[
            'id' => 1
        ]);
    }

    public function test_admin_user_can_delete_user(): void {

        $response = $this->useApiAs(User::find(2), 'delete', "{$this->apiBase}/users/1");

        $response->assertStatus(204);

        $this->assertDatabaseMissing('users',[
            'id' => 1
        ]);

    }

    public function test_admin_user_cant_delete_inexistent_user(): void {
        $response = $this->useApiAs(User::find(2), 'delete', "{$this->apiBase}/users/160");

        $response->assertStatus(404);
    }


}
