<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Tests\TestCase;

class TransactionSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function tokenFor(User $user): string
    {
        return JWTAuth::fromUser($user);
    }

    private function createTransactionFor(User $user): Transaction
    {
        return Transaction::create([
            'user_id' => $user->id,
            'transaction_number' => 'TRX-TEST-' . uniqid(),
            'type' => 'expense',
            'amount' => 10000,
            'status' => 'pending',
            'description' => 'Security Test Transaction',
            'transaction_date' => now(),
        ]);
    }

    public function test_transactions_require_authentication(): void
    {
        $this->getJson('/api/transactions')
            ->assertStatus(401);
    }

    public function test_invalid_token_is_rejected(): void
    {
        $this->withToken('this-is-not-a-valid-jwt')
            ->getJson('/api/transactions')
            ->assertStatus(401);
    }

    public function test_user_cannot_view_another_users_transaction(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $transaction = $this->createTransactionFor($userB);

        $token = $this->tokenFor($userA);

        $this->withToken($token)
            ->getJson("/api/transactions/{$transaction->id}")
            ->assertStatus(404)
            ->assertJson([
                'message' => 'Transaction not found',
            ]);
    }

    public function test_user_cannot_update_another_users_transaction(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $transaction = $this->createTransactionFor($userB);

        $token = $this->tokenFor($userA);

        $this->withToken($token)
            ->putJson("/api/transactions/{$transaction->id}", [
                'amount' => 999999,
                'status' => 'completed',
            ])
            ->assertStatus(404);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
            'amount' => 10000,
            'status' => 'pending',
        ]);
    }

    public function test_user_cannot_delete_another_users_transaction(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $transaction = $this->createTransactionFor($userB);

        $token = $this->tokenFor($userA);

        $this->withToken($token)
            ->deleteJson("/api/transactions/{$transaction->id}")
            ->assertStatus(404);

        $this->assertDatabaseHas('transactions', [
            'id' => $transaction->id,
        ]);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $this->postJson('/api/register', [
            'name' => 'Duplicate User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])
            ->assertStatus(422);
    }

    public function test_create_transaction_rejects_zero_amount(): void
    {
        $user = User::factory()->create();

        $token = $this->tokenFor($user);

        $this->withToken($token)
            ->postJson('/api/transactions', [
                'type' => 'expense',
                'amount' => 0,
                'description' => 'Invalid amount',
                'transaction_date' => now()->toDateTimeString(),
            ])
            ->assertStatus(422);
    }

    public function test_create_transaction_rejects_invalid_type(): void
    {
        $user = User::factory()->create();

        $token = $this->tokenFor($user);

        $this->withToken($token)
            ->postJson('/api/transactions', [
                'type' => 'invalid-type',
                'amount' => 10000,
                'description' => 'Invalid type',
                'transaction_date' => now()->toDateTimeString(),
            ])
            ->assertStatus(422);
    }

    public function test_user_lookup_by_email_requires_authentication(): void
    {
        $user = User::factory()->create([
            'email' => 'findme@example.com',
        ]);

        $this->getJson('/api/users/findme@example.com')
            ->assertStatus(401);
    }

    public function test_authenticated_user_can_lookup_user_by_email(): void
    {
        $user = User::factory()->create([
            'name' => 'Target User',
            'email' => 'target@example.com',
        ]);

        $token = $this->tokenFor($user);

        $this->withToken($token)
            ->getJson('/api/users/target@example.com')
            ->assertStatus(200)
            ->assertJson([
                'name' => 'Target User',
                'email' => 'target@example.com',
            ]);
    }
}
