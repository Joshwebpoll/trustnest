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
        Schema::create('cp_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('loan_number')->unique();
            $table->text('amount');
            $table->string('interest_rate');
            $table->integer('duration_months')->default(12);
            $table->text('monthly_repayment')->nullable();
            $table->text('total_payable')->nullable();
            $table->text('remaining_balance')->nullable();
            $table->text('total_paid')->nullable();
            $table->string('guarantor_user_id')->nullable();
            $table->string('guarantor_name')->nullable();
            $table->string('guarantor_email')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('application_date');
            $table->text('total_interest_paid')->nullable();
            $table->string("customer_account_number")->nullable();
            $table->string("membership_number")->nullable();
            $table->string("decreasing_amount")->nullable();
            $table->string("increasing_amount")->nullable();
            $table->text("over_paid")->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'disbursed', 'completed', 'defaulted'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('purpose');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_loans');
    }
};
