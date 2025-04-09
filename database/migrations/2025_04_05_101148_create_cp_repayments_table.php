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
        Schema::create('cp_repayments', function (Blueprint $table) {
            $table->id();
            $table->string('loan_id');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('repayment_amount');
            $table->text('remaining_balance');
            $table->string('payment_method');
            $table->string('interest_component')->nullable();
            $table->string('transaction_reference');
            $table->date('due_date');
            $table->date('repayment_date');
            $table->enum('status', ['pending', 'completed', 'processing'])->default('pending');
            $table->string('comment')->nullable();
            $table->string('updatedById')->nullable();
            $table->string('updatedEmail')->nullable();
            $table->string('updatedName')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_repayments');
    }
};
