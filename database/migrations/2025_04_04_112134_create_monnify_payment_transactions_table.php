<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('monnify_payment_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_reference')->unique();
            $table->string('payment_reference')->unique();
            $table->string('payment_description');
            $table->string('payment_method');
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('total_payable', 10, 2);
            $table->decimal('settlement_amount', 10, 2);
            $table->string('currency');
            $table->enum('payment_status', ['PAID', 'PENDING', 'FAILED']);
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('bank_code')->nullable();
            $table->decimal('amount_paid_from_bank', 10, 2)->nullable();
            $table->string('account_name')->nullable();
            $table->string('session_id')->nullable();
            $table->string('account_number')->nullable();
            $table->string('destination_bank_code')->nullable();
            $table->string('destination_bank_name')->nullable();
            $table->string('destination_account_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monnify_payment_transactions');
    }
};
