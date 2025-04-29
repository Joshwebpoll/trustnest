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
        Schema::create('cp_contributions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('cp_members')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('transaction_id');
            $table->enum('contribution_type', ['savings', 'shares', 'fee']);
            $table->text('amount_contributed');
            $table->string('reference_number');
            $table->string('account_number')->nullable();
            $table->string('payment_method');
            $table->date('contribution_date');
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->enum('contribution_deposit_type', ['cash', 'transfer']);
            $table->string('processed_by_id')->nullable();
            $table->string('processed_by_name')->nullable();
            $table->string('processed_by_email')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_contributions');
    }
};
