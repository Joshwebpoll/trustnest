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
        Schema::create('cp_interest_rates', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_amount', 15, 2)->nullable(); // Minimum loan amount
            $table->decimal('max_amount', 15, 2)->nullable(); // Maximum loan amount
            $table->decimal('interest_rate', 5, 2); // Interest rate percentage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_interest_rates');
    }
};
