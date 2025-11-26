<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('service_plan_id')->nullable()->constrained('service_plans');
            $table->enum('type', ['subscription', 'voucher', 'addon', 'manual'])->default('subscription');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('tax', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('total', 12, 2);
            $table->enum('status', ['draft', 'pending', 'paid', 'overdue', 'cancelled', 'refunded'])->default('pending');
            $table->date('issue_date');
            $table->date('due_date');
            $table->timestamp('paid_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->json('items')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_id')->unique();
            $table->foreignId('invoice_id')->nullable()->constrained('invoices');
            $table->foreignId('customer_id')->constrained('customers');
            $table->decimal('amount', 12, 2);
            $table->string('payment_method');
            $table->string('payment_channel')->nullable();
            $table->enum('status', ['pending', 'processing', 'success', 'failed', 'expired', 'refunded'])->default('pending');
            $table->string('external_id')->nullable();
            $table->string('payment_url')->nullable();
            $table->json('payment_data')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id')->unique();
            $table->foreignId('customer_id')->nullable()->constrained('customers');
            $table->foreignId('voucher_id')->nullable()->constrained('vouchers');
            $table->foreignId('invoice_id')->nullable()->constrained('invoices');
            $table->foreignId('payment_id')->nullable()->constrained('payments');
            $table->enum('type', ['income', 'expense', 'refund', 'topup', 'withdrawal'])->default('income');
            $table->string('category')->nullable();
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2)->default(0);
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('resellers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('code')->unique();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->decimal('balance', 12, 2)->default(0);
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('reseller_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reseller_id')->constrained('resellers');
            $table->enum('type', ['topup', 'sale', 'commission', 'withdrawal'])->default('sale');
            $table->decimal('amount', 12, 2);
            $table->decimal('balance_before', 12, 2)->default(0);
            $table->decimal('balance_after', 12, 2)->default(0);
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reseller_transactions');
        Schema::dropIfExists('resellers');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('invoices');
    }
};
