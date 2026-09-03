<?php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        private TransactionService $transactionService
    ) {}

    public function index()
    {
        $transactions = $this->transactionService->getAll();

        return response()->json([
            'message' => 'Transactions retrieved successfully',
            'data' => $transactions,
        ], 200);
    }

    public function show(int $id)
    {
        $transaction = $this->transactionService->findById($id);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Transaction retrieved successfully',
            'data' => $transaction,
        ], 200);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => ['required', 'string', 'in:income,expense'],
            'amount' => ['required', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['required', 'date'],
        ]);

        $transaction = $this->transactionService->create($validated);

        return response()->json([
            'message' => 'Transaction created successfully',
            'data' => $transaction,
        ], 201);
    }

    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'type' => ['sometimes', 'string', 'in:income,expense'],
            'amount' => ['sometimes', 'numeric', 'gt:0'],
            'description' => ['nullable', 'string', 'max:1000'],
            'transaction_date' => ['sometimes', 'date'],
            'status' => ['sometimes', 'string', 'in:pending,completed,cancelled'],
        ]);

        $transaction = $this->transactionService->update($id, $validated);

        if (!$transaction) {
            return response()->json([
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Transaction updated successfully',
            'data' => $transaction,
        ], 200);
    }

    public function destroy(int $id)
    {
        $deleted = $this->transactionService->delete($id);

        if (!$deleted) {
            return response()->json([
                'message' => 'Transaction not found',
            ], 404);
        }

        return response()->json([
            'message' => 'Transaction deleted successfully',
        ], 200);
    }
}