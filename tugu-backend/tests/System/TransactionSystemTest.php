<?php

namespace Tests\System;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_complete_full_transaction_lifecycle(): void
    {
        /*
         * ==========================================================
         * 1. REGISTER
         * ==========================================================
         */

        $registerResponse = $this->postJson('/api/register', [
            'name' => 'System Test User',
            'email' => 'systemtest@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $registerResponse
            ->assertCreated()
            ->assertJson([
                'message' => 'Registration successful',
            ])
            ->assertJsonStructure([
                'message',
                'token',
            ]);

        $user = User::where('email', 'systemtest@example.com')
            ->firstOrFail();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'System Test User',
            'email' => 'systemtest@example.com',
        ]);

        /*
         * ==========================================================
         * 2. LOGIN
         * ==========================================================
         */

        $loginResponse = $this->postJson('/api/login', [
            'login' => 'systemtest@example.com',
            'password' => 'password123',
        ]);

        $loginResponse
            ->assertOk()
            ->assertJson([
                'message' => 'Login successful',
            ])
            ->assertJsonStructure([
                'message',
                'token',
            ]);

        $token = $loginResponse->json('token');

        $this->assertNotEmpty($token);

        /*
         * ==========================================================
         * 3. CREATE TRANSACTION
         * ==========================================================
         */

        $createResponse = $this
            ->withToken($token)
            ->postJson('/api/transactions', [
                'type' => 'expense',
                'amount' => 150000,
                'description' => 'System Test Lunch',
                'transaction_date' => '2026-08-18 12:00:00',
            ]);

        $createResponse
            ->assertCreated()
            ->assertJson([
                'message' => 'Transaction created successfully',
            ])
            ->assertJsonPath(
                'data.type',
                'expense'
            )
            ->assertJsonPath(
                'data.amount',
                '150000.00'
            )
            ->assertJsonPath(
                'data.status',
                'pending'
            )
            ->assertJsonPath(
                'data.description',
                'System Test Lunch'
            );

        $transactionId = $createResponse->json('data.id');

        $this->assertNotNull($transactionId);

        /*
         * ==========================================================
         * 4. VERIFY DATABASE STATE AFTER CREATE
         * ==========================================================
         */

        $this->assertDatabaseHas('transactions', [
            'id' => $transactionId,
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => 150000,
            'status' => 'pending',
            'description' => 'System Test Lunch',
        ]);

        /*
         * ==========================================================
         * 5. READ TRANSACTION
         * ==========================================================
         */

        $getResponse = $this
            ->withToken($token)
            ->getJson("/api/transactions/{$transactionId}");

        $getResponse
            ->assertOk()
            ->assertJson([
                'message' => 'Transaction retrieved successfully',
            ])
            ->assertJsonPath(
                'data.id',
                $transactionId
            )
            ->assertJsonPath(
                'data.user_id',
                $user->id
            );

        /*
         * ==========================================================
         * 6. UPDATE TRANSACTION
         * ==========================================================
         */

        $updateResponse = $this
            ->withToken($token)
            ->putJson("/api/transactions/{$transactionId}", [
                'amount' => 175000,
                'status' => 'completed',
                'description' => 'Updated System Test Lunch',
            ]);

        $updateResponse
            ->assertOk()
            ->assertJson([
                'message' => 'Transaction updated successfully',
            ])
            ->assertJsonPath(
                'data.amount',
                '175000.00'
            )
            ->assertJsonPath(
                'data.status',
                'completed'
            )
            ->assertJsonPath(
                'data.description',
                'Updated System Test Lunch'
            );

        /*
         * ==========================================================
         * 7. VERIFY DATABASE STATE AFTER UPDATE
         * ==========================================================
         */

        $this->assertDatabaseHas('transactions', [
            'id' => $transactionId,
            'user_id' => $user->id,
            'amount' => 175000,
            'status' => 'completed',
            'description' => 'Updated System Test Lunch',
        ]);

        /*
         * ==========================================================
         * 8. DELETE TRANSACTION
         * ==========================================================
         */

        $deleteResponse = $this
            ->withToken($token)
            ->deleteJson("/api/transactions/{$transactionId}");

        $deleteResponse
            ->assertOk()
            ->assertJson([
                'message' => 'Transaction deleted successfully',
            ]);

        /*
         * ==========================================================
         * 9. VERIFY DATABASE STATE AFTER DELETE
         * ==========================================================
         */

        $this->assertDatabaseMissing('transactions', [
            'id' => $transactionId,
        ]);

        /*
         * ==========================================================
         * 10. VERIFY API RETURNS NOT FOUND
         * ==========================================================
         */

        $this
            ->withToken($token)
            ->getJson("/api/transactions/{$transactionId}")
            ->assertNotFound()
            ->assertJson([
                'message' => 'Transaction not found',
            ]);
    }
}