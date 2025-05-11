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
        Schema::create('cp_bank_names', function (Blueprint $table) {
            $table->id();
            $table->string('bank_name');
            $table->string('bank_code');
            $table->string('bank_type')->nullable();
            $table->string('country_code')->nullable();
            $table->string('currency_code')->nullable();
            $table->enum('status', ['enable', 'disable'])->default('enable');
            $table->string('payment_gate_type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_bank_names');
    }
};
