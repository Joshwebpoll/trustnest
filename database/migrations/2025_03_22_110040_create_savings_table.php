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
        Schema::create('savings', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_id');
            $table->string("account_number");
            $table->text('amount_deposited');
            $table->enum('saving_type', ['current', 'saving']);
            $table->enum('status', ['pending', 'completed'])->default('pending');
            $table->string('transaction_reference')->unique();
            $table->enum('deposit_type', ['cash', 'transfer']);
            $table->string('processed_by');
            $table->timestamp('deposit_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('savings');
    }
};
