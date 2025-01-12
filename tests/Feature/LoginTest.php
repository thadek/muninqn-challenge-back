<?php

namespace Tests\Feature;

use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(UserSeeder::class);
    }


    public function test_existing_user_can_login(): void
    {
        # Teniendo
        $credentials = ['email' => 'example@example.com', 'password' => 'password'];

        # Haciendo
        $response = $this->post("{$this->apiBase}/login", $credentials);



        # Esperando
        $response->assertStatus(200);


        $response->assertJsonStructure(['data'=>['access_token','token_type','expires_in']]); // Assuming a token is returned on successful login
    }


    public function test_non_existing_user_cannot_login(): void
    {
        # Teniendo
        $credentials = ['email' => 'nonexistent@example.com', 'password' => 'wrongpassword'];

        # Haciendo
        $response = $this->post("{$this->apiBase}/login", $credentials);

        # Esperando
        $response->assertStatus(401); // Assuming 401 Unauthorized for invalid credentials

    }


    public function test_email_is_required(): void
    {
        # Teniendo
        $credentials = ['password' => 'password']; // Email field omitted

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['email']);
    }


    public function test_password_is_required(): void
    {
        # Teniendo
        $credentials = ['email' => 'email@email.com']; // Password Field omitted

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);

        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['password']);
    }

    public function test_valid_email_is_required():void
    {
        # Teniendo
        $credentials = ['email'=>'email',
            'password' => 'password']; // Email field invalid

        # Haciendo
        $response = $this->postJson("{$this->apiBase}/login", $credentials);


        # Esperando
        $response->assertStatus(422); // Assuming 422 Unprocessable Entity for validation errors
        $response->assertJsonValidationErrors(['email']);
    }





}
