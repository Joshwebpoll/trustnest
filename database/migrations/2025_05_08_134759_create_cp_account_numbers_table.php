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
        Schema::create('cp_account_numbers', function (Blueprint $table) {
            $table->id();
            $table->string('bank_account_name');
            $table->string('bank_account_number');
            $table->string('bank_name');
            $table->string('bank_code')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('country_code')->nullable();
            $table->string('currency_code')->nullable();
            $table->enum('status', ['enable', 'disable'])->default('enable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_account_numbers');
    }
};
