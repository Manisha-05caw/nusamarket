<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('order_id')->unique()->constrained('orders')->restrictOnDelete();
            $table->enum('method', [
                'bank_transfer', 'gopay', 'ovo', 'dana',
                'qris', 'credit_card', 'debit_card',
            ]);
            $table->string('gateway', 50)->default('midtrans');
            $table->string('gateway_ref', 255)->nullable()->index();
            $table->json('gateway_payload')->nullable();
            $table->decimal('amount', 14, 2);
            $table->enum('status', ['pending', 'paid', 'failed', 'expired', 'refunded'])
                  ->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->timestamps();

            $table->index('status');
        });

        Schema::create('seller_balances', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->unique()->constrained('stores')->cascadeOnDelete();
            $table->decimal('available', 14, 2)->default(0);
            $table->decimal('pending', 14, 2)->default(0);
            $table->decimal('total_earned', 14, 2)->default(0);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
        });

        Schema::create('balance_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignUuid('order_id')->nullable()->constrained('orders')->nullOnDelete();
            $table->string('type', 30)->comment('credit_sale | debit_withdrawal | debit_refund');
            $table->decimal('amount', 14, 2);
            $table->decimal('balance_after', 14, 2);
            $table->text('description')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index('store_id');
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('balance_transactions');
        Schema::dropIfExists('seller_balances');
        Schema::dropIfExists('payments');
    }
};
