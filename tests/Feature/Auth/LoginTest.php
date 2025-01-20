<?php

namespace Auth;

use Database\Seeders\RoleSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RoleSeeder::class);
        $this->seed(UserSeeder::class);
    }


    public function test_existing_user_can_login(): void
    {
        # Teniendo
        $credentials = ['identifier' => 'example@example.com', 'password' => 'password'];

        # Haciendo
        $response = $this->post("{$this->apiBase}/auth/login", $credentials);



        # Esperando
        $response->assertStatus(200);



        $response->assertJsonStructure(['data'=>['token','expires_in']]); // Assuming a token is returned on successful login
    }


    public function test_non_existing_user_cannot_login(): void
    {
        # Teniendo
        $credentials = ['identifier' => 'nonexistent@example.com', 'password' => 'wrongpassword'];

        # Haciendo
        $response = $this->post("{$this->apiBase}/auth/login", $credentials);

      //  dd($response->getContent());
        # Esperando
        $response->assertStatus(401); // Assuming 401 Unauthorized for invalid credentials

    }


    public function test_identifier_is_required(): void
    {
        # Teniendo
        $credentials = ['password' => 'password']; // Email field omitted

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/auth/login", $credentials);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['identifier']);
    }


    public function test_password_is_required(): void
    {
        # Teniendo
        $credentials = ['identifier' => 'email@email.com']; // Password Field omitted

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/auth/login", $credentials);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['password']);
    }







}
