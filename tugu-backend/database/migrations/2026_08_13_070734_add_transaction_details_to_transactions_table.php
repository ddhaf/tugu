<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transaction_number')->nullable();
            $table->string('type')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->string('status')->default('pending');
            $table->text('description')->nullable();
            $table->timestamp('transaction_date')->nullable();
        });

        // Fill existing transactions so the new columns are not null.
        DB::table('transactions')
            ->orderBy('id')
            ->each(function ($transaction) {
                DB::table('transactions')
                    ->where('id', $transaction->id)
                    ->update([
                        'transaction_number' => 'TRX-' . str_pad((string) $transaction->id, 6, '0', STR_PAD_LEFT),
                        'type' => 'expense',
                        'amount' => 0,
                        'transaction_date' => $transaction->created_at,
                    ]);
            });

        Schema::table('transactions', function (Blueprint $table) {
            $table->string('transaction_number')->unique()->nullable(false)->change();
            $table->string('type')->nullable(false)->change();
            $table->decimal('amount', 15, 2)->nullable(false)->change();
            $table->timestamp('transaction_date')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropUnique(['transaction_number']);

            $table->dropColumn([
                'transaction_number',
                'type',
                'amount',
                'status',
                'description',
                'transaction_date',
            ]);
        });
    }
};