<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BasicTest extends TestCase
{


   public function test_endpoint_api_test_returns_status_success_and_json(): void
   {
       $response = $this->get("{$this->apiBase}/test");

       $response->assertStatus(200);
       $response->assertJson(['message' => 'test']);
    }


}
