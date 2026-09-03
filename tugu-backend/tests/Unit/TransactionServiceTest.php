<?php

namespace Tests\Unit;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Models\User;
use App\Services\TransactionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    private function authenticatedUser(): User
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api');

        return $user;
    }

    public function test_get_all_returns_transactions_for_authenticated_user(): void
    {
        $user = $this->authenticatedUser();

        $transactions = new \Illuminate\Database\Eloquent\Collection([
            new Transaction([
                'id' => 1,
                'user_id' => $user->id,
                'type' => 'expense',
                'amount' => 10000,
                'status' => 'pending',
            ]),
        ]);

        $repository = Mockery::mock(TransactionRepositoryInterface::class);

        $repository
            ->shouldReceive('getAllByUserId')
            ->once()
            ->with($user->id)
            ->andReturn($transactions);

        $service = new TransactionService($repository);

        $result = $service->getAll();

        $this->assertSame($transactions, $result);
    }

    public function test_find_by_id_returns_transaction_for_authenticated_user(): void
    {
        $user = $this->authenticatedUser();

        $transaction = new Transaction([
            'id' => 1,
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => 10000,
            'status' => 'pending',
        ]);

        $repository = Mockery::mock(TransactionRepositoryInterface::class);

        $repository
            ->shouldReceive('findByIdForUser')
            ->once()
            ->with(1, $user->id)
            ->andReturn($transaction);

        $service = new TransactionService($repository);

        $result = $service->findById(1);

        $this->assertSame($transaction, $result);
    }

    public function test_create_adds_user_id_transaction_number_and_pending_status(): void
    {
        $user = $this->authenticatedUser();

        $input = [
            'type' => 'expense',
            'amount' => 150000,
            'description' => 'Test transaction',
            'transaction_date' => '2026-08-20 10:00:00',
        ];

        $expectedTransaction = new Transaction([
            'id' => 1,
            'user_id' => $user->id,
            'type' => 'expense',
            'amount' => 150000,
            'status' => 'pending',
            'description' => 'Test transaction',
            'transaction_date' => '2026-08-20 10:00:00',
        ]);

        $repository = Mockery::mock(TransactionRepositoryInterface::class);

        $repository
            ->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function (array $data) use ($user) {
                return $data['user_id'] === $user->id
                    && $data['type'] === 'expense'
                    && $data['amount'] === 150000
                    && $data['description'] === 'Test transaction'
                    && $data['status'] === 'pending'
                    && isset($data['transaction_number'])
                    && str_starts_with($data['transaction_number'], 'TRX-');
            }))
            ->andReturn($expectedTransaction);

        $service = new TransactionService($repository);

        $result = $service->create($input);

        $this->assertSame($expectedTransaction, $result);
    }

    public function test_update_passes_transaction_data_and_user_id_to_repository(): void
    {
        $user = $this->authenticatedUser();

        $data = [
            'amount' => 200000,
            'status' => 'completed',
            'description' => 'Updated transaction',
        ];

        $expectedTransaction = new Transaction([
            'id' => 1,
            'user_id' => $user->id,
            'amount' => 200000,
            'status' => 'completed',
            'description' => 'Updated transaction',
        ]);

        $repository = Mockery::mock(TransactionRepositoryInterface::class);

        $repository
            ->shouldReceive('updateForUser')
            ->once()
            ->with(1, $data, $user->id)
            ->andReturn($expectedTransaction);

        $service = new TransactionService($repository);

        $result = $service->update(1, $data);

        $this->assertSame($expectedTransaction, $result);
    }

    public function test_delete_returns_true_when_repository_deletes_transaction(): void
    {
        $user = $this->authenticatedUser();

        $repository = Mockery::mock(TransactionRepositoryInterface::class);

        $repository
            ->shouldReceive('deleteForUser')
            ->once()
            ->with(1, $user->id)
            ->andReturn(true);

        $service = new TransactionService($repository);

        $result = $service->delete(1);

        $this->assertTrue($result);
    }

    public function test_delete_returns_false_when_repository_fails(): void
    {
        $user = $this->authenticatedUser();

        $repository = Mockery::mock(TransactionRepositoryInterface::class);

        $repository
            ->shouldReceive('deleteForUser')
            ->once()
            ->with(1, $user->id)
            ->andReturn(false);

        $service = new TransactionService($repository);

        $result = $service->delete(1);

        $this->assertFalse($result);
    }
}