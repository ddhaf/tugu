<?php

namespace App\Services;

use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use Illuminate\Support\Str;

class TransactionService
{
    public function __construct(
        private TransactionRepositoryInterface $transactionRepository
    ) {}

    public function getAll()
    {
        $userId = auth('api')->id();

        return $this->transactionRepository->getAllByUserId($userId);
    }

    public function findById(int $id)
    {
        $userId = auth('api')->id();

        return $this->transactionRepository->findByIdForUser(
            $id,
            $userId
        );
    }

    public function create(array $data): Transaction
    {
        $data['user_id'] = auth('api')->id();

        $data['transaction_number'] =
            'TRX-' .
            now()->format('YmdHis') .
            '-' .
            strtoupper(Str::random(6));

        $data['status'] = 'pending';

        return $this->transactionRepository->create($data);
    }

    public function update(int $id, array $data)
    {
        $userId = auth('api')->id();

        return $this->transactionRepository->updateForUser(
            $id,
            $data,
            $userId
        );
    }

    public function delete(int $id): bool
    {
        $userId = auth('api')->id();

        return $this->transactionRepository->deleteForUser(
            $id,
            $userId
        );
    }
}