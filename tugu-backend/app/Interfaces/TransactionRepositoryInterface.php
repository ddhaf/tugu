<?php

namespace App\Interfaces;

use App\Models\Transaction;
use Illuminate\Database\Eloquent\Collection;

interface TransactionRepositoryInterface
{
    public function getAllByUserId(int $userId): Collection;

    public function findByIdForUser(int $id, int $userId): ?Transaction;

    public function create(array $data): Transaction;

    public function updateForUser(
        int $id,
        array $data,
        int $userId
    ): ?Transaction;

    public function deleteForUser(
        int $id,
        int $userId
    ): bool;
}