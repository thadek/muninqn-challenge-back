<?php

namespace Task;

use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Task;

class EditTaskTest extends TestCase
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



    /**
     * Test que comprueba si un admin autenticado puede editar una tarea (happy path)
     */
    public function test_an_authenticated_admin_can_modify_task_without_users(): void
    {

        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'en progreso',
            'priority' => 'alta'];

        #Haciendo
        $response = $this->useApiAs(User::find(2),'put',"{$this->apiBase}/tasks/{$this->task->id}", $data);
        #Esperando

       // dd($response->getContent());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data'=>['task'=>['title','description','status','priority','created_at','updated_at']]]);
        $this->assertDatabaseCount('tasks', 1);
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Tarea 1',
            'description' => 'Test de edit task',
            'status' => 'pendiente',
            'priority' => 'alta',
        ]);
    }




    /**
     * Test que comprueba si un admin autenticado puede editar una tarea con un usuario asignado
     */
    public function test_an_authenticated_admin_can_edit_task_with_an_assigned_user(): void
    {

        $this->task->users()->attach(1);

        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'en progreso',
            'priority' => 'alta'];

        #Haciendo
        $response = $this->useApiAs(User::find(1),'put',"{$this->apiBase}/tasks/{$this->task->id}", $data);
        #Esperando

        // dd($response->getContent());

        $response->assertStatus(200);
        $response->assertJsonStructure(['data'=>['task'=>['title','description','status','priority','created_at','updated_at']]]);
        $this->assertDatabaseCount('tasks', 1);
        $this->assertDatabaseMissing('tasks', [
            'title' => 'Tarea 1',
            'description' => 'Test de edit task',
            'status' => 'pendiente',
            'priority' => 'alta',
        ]);
    }


    public function test_an_authenticated_admin_can_assign_an_user_to_a_task(): void{
        $user = User::factory()->create();

        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'en progreso',
            'priority' => 'alta',
            'users' => [$user->id]
        ];

        #Haciendo
        $response = $this->useApiAs(User::find(2), 'put', "{$this->apiBase}/tasks/{$this->task->id}", $data);

        #Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['task' => ['title', 'description', 'status', 'priority', 'created_at', 'updated_at', 'users']]]);
        $this->assertDatabaseCount('tasks', 1);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'en progreso',
            'priority' => 'alta',
        ]);
        $this->assertDatabaseHas('task_user', [
            'task_id' => $this->task->id,
            'user_id' => $user->id,
        ]);


    }


    public function test_an_authenticated_admin_can_remove_an_user_from_a_task_using_put(): void{
        $user = User::factory()->create();
        $this->task->users()->attach($user->id);

        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'en progreso',
            'priority' => 'alta',
            'users' => []
        ];

        #Haciendo
        $response = $this->useApiAs(User::find(2), 'put', "{$this->apiBase}/tasks/{$this->task->id}", $data);

        #Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['task' => ['title', 'description', 'status', 'priority', 'created_at', 'updated_at']]]);
        $this->assertDatabaseMissing('task_user', [
            'task_id' => $this->task->id,
            'user_id' => $user->id,
        ]);

    }
    public function test_an_authenticated_admin_can_remove_an_user_from_a_task_using_patch(): void{
        $user = User::factory()->create();
        $this->task->users()->attach($user->id);

        #Teniendo
        $data = [
            'users' => [] // Eliminando todos los usuarios
        ];




        #Haciendo
        $response = $this->useApiAs(User::find(2), 'patch', "{$this->apiBase}/tasks/{$this->task->id}", $data);

        #Esperando
        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => ['task' => ['title', 'description', 'status', 'priority', 'created_at', 'updated_at']]]);
        $this->assertDatabaseMissing('task_user', [
            'task_id' => $this->task->id,
            'user_id' => $user->id,
        ]);

    }


    //TODO: caso usuario solo puede editar si termino o no su tarea
    //TODO: caso

    public function test_an_non_authenticated_user_cannot_modify_a_task(): void
    {
        $user = User::factory()->create();

        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'en progreso',
            'priority' => 'alta',
            'users' => [$user->id]
        ];

        #Haciendo
        $response = $this->putJson( "{$this->apiBase}/tasks/{$this->task->id}", $data);

        #Esperando
        $response->assertStatus(403);
    }

    public function test_title_is_required(): void
    {
        #Teniendo
        $data = [
            'description' => 'Realizar ABM de algo',
            'status' => 'pendiente',
            'priority' => 'alta']; //Sin title

        #Haciendo
        $response = $this->useApiAs(User::find(1),'put',"{$this->apiBase}/tasks/{$this->task->id}", $data);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['title']);
    }

    public function test_description_is_required(): void
    {
        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'status' => 'pendiente',
            'priority' => 'alta']; //Sin description

        #Haciendo
        $response = $this->useApiAs(User::find(1),'put',"{$this->apiBase}/tasks/{$this->task->id}", $data);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['description']);
    }



    public function test_status_is_required(): void
    {
        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'priority' => 'alta']; //Sin status

        #Haciendo
        $response = $this->useApiAs(User::find(1),'put',"{$this->apiBase}/tasks/{$this->task->id}", $data);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_status_must_be_valid_enum_value(): void
    {
        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'cualquiercosa',
            'priority' => 'alta']; //Status incorrecto

        #Haciendo
        $response = $this->useApiAs(User::find(1),'put',"{$this->apiBase}/tasks/{$this->task->id}", $data);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_priority_is_required(): void
    {
        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status'=>'pendiente'
            ]; //Sin PRIORITY

        #Haciendo
        $response = $this->useApiAs(User::find(1),'put',"{$this->apiBase}/tasks/{$this->task->id}", $data);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['priority']);
    }

    public function test_priority_must_be_valid_enum_value(): void
    {
        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'pendiente',
            'priority' => 'cualquiera']; //priority incorrecto

        #Haciendo
        $response = $this->useApiAs(User::find(1),'put',"{$this->apiBase}/tasks/{$this->task->id}", $data);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['priority']);
    }





}
