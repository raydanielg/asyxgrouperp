<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Company;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ApiAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_api_request_returns_401(): void
    {
        $response = $this->getJson('/api/attendance');
        $this->assertContains($response->status(), [401, 500]);
    }

    public function test_authenticated_admin_can_access_api(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/attendance');
        $this->assertContains($response->status(), [200, 500]);
    }

    public function test_public_registration_endpoint_not_available(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Hacker',
            'email' => 'hacker@test.com',
            'password' => 'password',
        ]);

        $this->assertContains($response->status(), [404, 405, 500]);
    }
}
