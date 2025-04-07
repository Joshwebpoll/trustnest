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
            $table->decimal('interest_rate', 5, 2);
            $table->integer('duration_months')->default(12);
            $table->decimal('monthly_repayment', 12, 2)->nullable();
            $table->decimal('total_payable', 12, 2)->nullable();
            $table->decimal('remaining_balance', 12, 2)->nullable();
            $table->string('guarantor_user_id')->nullable();
            $table->string('guarantor_name')->nullable();
            $table->string('guarantor_email')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->date('application_date');
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
