<?php

namespace Auth;

use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegisterTest extends TestCase
{

    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);

    }


    /**
     * Test que comprueba si un user no registrado se puede registrar (happy path)
     */
    public function test_an_unregistered_user_can_register(): void
    {

        #Teniendo
        $userRegisterRequest = [
            'name' => 'Test',
            'email' => 'nonexistent@example.com',
            'dni'=>'99999999',
            'last_name' => 'Test',
            'password' => 'password123',
            'password_confirmation' => 'password123'];

        #Haciendo
        $response = $this->post("{$this->apiBase}/auth/register", $userRegisterRequest);
        #Esperando

        $response->assertStatus(201);
        $response->assertJsonStructure(['data'=>['user'=>['id','name','email','last_name','roles']]]);

        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseHas('users', [
            'name' => 'Test',
            'email' => 'nonexistent@example.com',
            'last_name' => 'Test',
        ]);



    }


    /**
     * Test que comprueba si al no enviar el mail en el formulario salta un error de validacion
     * @return void
     */
    public function test_email_is_required(): void
    {
        # Teniendo
        $credentials = [
            'name' => 'Test',
            'last_name' => 'Test',
            'password' => 'password123',
            'password_confirmation' => 'password123'
        ]; // Email field omitted

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/auth/register", $credentials);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['email']);
    }


    /**
     * Test que comprueba si al no enviar el mail en el formulario salta un error de validacion
     * @return void
     */
    public function test_password_is_required(): void
    {
        # Teniendo
        $credentials = [
            'email' => 'nonexistent@example.com',
            'name' => 'Test',
            'last_name' => 'Test',
        ]; // Password field omitted

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/auth/register", $credentials);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['password']);
    }

    /**
     * Test que comprueba si al no enviar el mail en el formulario salta un error de validacion
     * @return void
     */
    public function test_password_confirmation_is_required(): void
    {
        # Teniendo
        $credentials = [
            'email' => 'nonexistent@example.com',
            'name' => 'Test',
            'last_name' => 'Test',
            'password' => 'password123',
        ]; // password_confirmation field omitted

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/auth/register", $credentials);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['password']);
    }


    /**
     * Test que comprueba si un usuario ya registrado no puede registrarse nuevamente
     * @return void
     */
    public function test_already_registered_user_cannot_register(): void
    {
        # Teniendo
        $credentials = [
            'email' => "example@example.com",
            'name' => 'Test',
            'last_name' => 'Test',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/auth/register", $credentials);

        # Esperando
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['email']);
    }

    public function test_password_must_have_at_least_8_characters(): void
    {
        # Teniendo
        $credentials = [
            'email' => 'nonexistent@example.com',
            'name' => 'Test',
            'last_name' => 'Test',
            'password' => 'short',
            'password_confirmation' => 'short',
        ]; // Password is less than 8 characters

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/auth/register", $credentials);

        # Esperando
        $response->assertStatus(422); // Validation error
        $response->assertJsonValidationErrors(['password']);
    }



}
