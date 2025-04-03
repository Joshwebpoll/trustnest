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
        Schema::create('unique_bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('bank_id');
            $table->string('contract_code');
            $table->string('account_reference');
            $table->string('account_name');
            $table->string('currency_code');
            $table->string('customer_email');
            $table->string('customer_name');
            $table->string('collection_channel');
            $table->string('reservation_reference');
            $table->string('reserved_account_type');
            $table->string('status');
            $table->timestamp('created_on');
            $table->string('bvn');
            $table->boolean('restrict_payment_source');
            $table->string("user_id");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unique_bank_accounts');
    }
};
