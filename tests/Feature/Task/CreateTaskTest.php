<?php

namespace Task;

use App\Enums\Roles;
use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class CreateTaskTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);


    }


    /**
     * Test que comprueba si un admin autenticado puede crear una tarea sin usuarios asignados (happy path)
     */
    public function test_an_authenticated_admin_can_create_task_without_users(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Roles::ADMIN->value);

        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'pendiente',
            'priority' => 'alta'];

        #Haciendo
        $response = $this->useApiAs($admin,'post',"{$this->apiBase}/tasks", $data);
        #Esperando

        //dd($response->getContent());

        $response->assertStatus(201);
        $response->assertJsonStructure(['data'=>['task'=>['title','description','status','priority','created_at','updated_at']]]);
        $this->assertDatabaseCount('tasks', 1);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'pendiente',
            'priority' => 'alta',
        ]);
    }


    /**
     * Test que comprueba si un admin autenticado puede crear una tarea con un usuario asignado
     */
    public function test_an_authenticated_admin_can_create_task_with_an_assigned_user(): void
    {
        $user = User::factory()->create();

        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'pendiente',
            'priority' => 'alta',
            'users'=>[$user->id]];

        #Haciendo
        $response = $this->useApiAs(User::find(2),'post',"{$this->apiBase}/tasks", $data);
        #Esperando



        $response->assertStatus(201);
        $response->assertJsonStructure(['data'=>['task'=>['title','description','status','priority','created_at','updated_at','users']]]);
        $this->assertDatabaseCount('tasks', 1);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'pendiente',
            'priority' => 'alta',
        ]);
        $this->assertDatabaseHas('task_user', [
            'task_id' => 1,
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test que comprueba si un admin autenticado puede crear una tarea con 2 usuarios asignados
     */
    public function test_an_authenticated_admin_can_create_task_with_2_assigned_users(): void
    {
        $user = User::factory()->create();
        $user2 = User::factory()->create();

        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'pendiente',
            'priority' => 'alta',
            'users'=>[$user->id,$user2->id]];

        #Haciendo
        $response = $this->useApiAs(User::find(2),'post',"{$this->apiBase}/tasks", $data);
        #Esperando
        $response->assertStatus(201);
        $response->assertJsonStructure(['data'=>['task'=>['title','description','status','priority','created_at','updated_at','users']]]);
        $this->assertDatabaseCount('tasks', 1);
        $this->assertDatabaseHas('tasks', [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'pendiente',
            'priority' => 'alta',
        ]);
        $this->assertDatabaseHas('task_user', [
            'task_id' => 1,
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('task_user', [
            'task_id' => 1,
            'user_id' => $user2->id,
        ]);
    }

    public function test_an_non_authenticated_user_cannot_modify_a_task(): void
    {
        #Teniendo
        $data = [
            'title' => 'Tarea 1',
            'description' => 'Realizar ABM de algo',
            'status' => 'en progreso',
            'priority' => 'alta'
        ];

        #Haciendo
        $response = $this->postJson( "{$this->apiBase}/tasks", $data);

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
        $response = $this->useApiAs(User::find(1),'post',"{$this->apiBase}/tasks", $data);

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
        $response = $this->useApiAs(User::find(1),'post',"{$this->apiBase}/tasks", $data);

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
        $response = $this->useApiAs(User::find(1),'post',"{$this->apiBase}/tasks", $data);

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
        $response = $this->useApiAs(User::find(1),'post',"{$this->apiBase}/tasks", $data);

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
        $response = $this->useApiAs(User::find(1),'post',"{$this->apiBase}/tasks", $data);

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
        $response = $this->useApiAs(User::find(1),'post',"{$this->apiBase}/tasks", $data);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['priority']);
    }





}
