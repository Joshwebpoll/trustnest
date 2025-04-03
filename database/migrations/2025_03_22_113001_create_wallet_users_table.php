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
        Schema::create('wallet_users', function (Blueprint $table) {
            $table->id();
            $table->string('wallet_id');
            $table->text('wallet_balance');
            $table->string('balanace_before')->nullable();
            $table->string('balanace_after')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_users');
    }
};
