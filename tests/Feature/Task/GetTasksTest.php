<?php

namespace Tests\Feature\Task;

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\TaskSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GetTasksTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $this->seed(TaskSeeder::class);

    }


    public function test_get_tasks_endpoint_as_admin_returns_all_tasks(){

        $response = $this->useApiAs(User::find(2), 'get', "{$this->apiBase}/tasks");

        $response->assertStatus(200);
        $response->assertJsonStructure(['data'=>[['id','title','description','status','priority','users']]]);
        $response->assertJsonCount(3);

    }
}
