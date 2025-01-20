<?php

namespace Task;

use App\Models\Task;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
        $this->task = Task::factory()->create([
            'title' => 'Tarea 1',
            'description' => 'Test de edit task',
            'status' => 'pendiente',
            'priority' => 'alta',
        ]);
    }



    public function test_an_authenticated_admin_can_delete_a_task(){
        #Haciendo
        $response = $this->useApiAs(User::find(2),'delete',"{$this->apiBase}/tasks/{$this->task->id}");

        #Esperando
        $response->assertStatus(200);
        $this->assertDatabaseCount('tasks', 0);
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Tarea 1',
            'description' => 'Test de edit task',
            'status' => 'pendiente',
            'priority' => 'alta',
        ]);
    }


    public function test_an_authenticated_admin_can_delete_a_task_with_users(){

        #Teniendo
        $this->task->users()->attach(1);
        #Haciendo
        $response = $this->useApiAs(User::find(2),'delete',"{$this->apiBase}/tasks/{$this->task->id}");

        #Esperando
        $response->assertStatus(200);
        $this->assertDatabaseCount('tasks', 0);
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Tarea 1',
            'description' => 'Test de edit task',
            'status' => 'pendiente',
            'priority' => 'alta',
        ]);
        $this->assertDatabaseMissing('task_user', [
            'task_id' => $this->task->id,
            'user_id' => 1,
        ]);

    }


    public function test_an_unauthenticated_user_cannot_delete_a_task(){

        $response = $this->delete("{$this->apiBase}/tasks/{$this->task->id}");
        $response->assertStatus(403);
        $this->assertDatabaseCount('tasks', 1);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarea 1',
            'description' => 'Test de edit task',
            'status' => 'pendiente',
            'priority' => 'alta',
        ]);
    }

}
