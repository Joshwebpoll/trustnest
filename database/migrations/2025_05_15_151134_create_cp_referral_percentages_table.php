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
        Schema::create('cp_referral_percentages', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_amount', 15, 2)->nullable(); // Minimum loan amount
            $table->decimal('max_amount', 15, 2)->nullable(); // Maximum loan amount
            $table->decimal('referral_reward_percent', 5, 2); // referral reward percentage
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_referral_percentages');
    }
};
