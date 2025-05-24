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
        Schema::table('cp_repayments', function (Blueprint $table) {
            $table->text('interest_paid')->after('status')->nullable();
            $table->text('next_interest_paid')->after('interest_paid')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cp_repayments', function (Blueprint $table) {
            Schema::dropIfExists('interest_paid');
            Schema::dropIfExists('next_interest_paid');
        });
    }
};
