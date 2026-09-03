<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_is_rate_limited_after_ten_requests(): void
    {
        $payload = [
            'login' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ];

        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/login', $payload);
            $this->assertContains($response->status(), [401, 422]);
        }

        $this->postJson('/api/login', $payload)
            ->assertStatus(429);
    }

    public function test_register_is_rate_limited_after_ten_requests(): void
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'short', // intentionally invalid to get 422
            'password_confirmation' => 'short',
        ];

        for ($i = 0; $i < 10; $i++) {
            $response = $this->postJson('/api/register', $payload);
            $this->assertContains($response->status(), [201, 422]);
        }

        $this->postJson('/api/register', $payload)
            ->assertStatus(429);
    }

    public function test_normal_login_succeeds_under_rate_limit(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $this->postJson('/api/login', [
            'login' => $user->email,
            'password' => 'password123',
        ])
            ->assertStatus(200)
            ->assertJsonStructure(['message', 'token']);
    }

    public function test_normal_register_succeeds_under_rate_limit(): void
    {
        $this->postJson('/api/register', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertStatus(201)
            ->assertJsonStructure(['message', 'token']);
    }
}
