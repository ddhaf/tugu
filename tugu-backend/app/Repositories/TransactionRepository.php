<?php

namespace App\Repositories;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function getAllByUserId(int $userId): Collection
    {
        return Transaction::with('user')
            ->where('user_id', $userId)
            ->get();
    }

    public function findByIdForUser(int $id, int $userId): ?Transaction
    {
        return Transaction::with('user')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function create(array $data): Transaction
    {
        return Transaction::create($data);
    }

    public function updateForUser(
        int $id,
        array $data,
        int $userId
    ): ?Transaction {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$transaction) {
            return null;
        }

        $transaction->update($data);

        return $transaction->fresh();
    }

    public function deleteForUser(
        int $id,
        int $userId
    ): bool {
        $transaction = Transaction::where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$transaction) {
            return false;
        }

        return (bool) $transaction->delete();
    }
}