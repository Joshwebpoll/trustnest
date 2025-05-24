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
        Schema::table('cp_members', function (Blueprint $table) {
            $table->text('opening_balance')->after('total_savings')->nullable(); // Minimum loan amount
            $table->text('closing_balance')->after('opening_balance')->nullable(); // Maximum loan amount
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cp_members', function (Blueprint $table) {
            Schema::dropIfExists('opening_balance');
            Schema::dropIfExists('closing_balance');
        });
    }
};
