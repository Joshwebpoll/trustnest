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
        Schema::create('cp_refund_over_pays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('cp_loans')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('refund_amount');
            $table->enum('status', ['pending', 'refunded', 'failed', 'completed'])->default('pending'); // pending, processed, failed
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_refund_over_pays');
    }
};
