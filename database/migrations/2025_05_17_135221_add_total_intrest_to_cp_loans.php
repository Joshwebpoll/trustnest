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
        Schema::table('cp_loans', function (Blueprint $table) {
            // $table->text('total_interest_paid')->after('status')->nullable();
            // $table->string("customer_account_number")->after('total_interest_paid')->nullable();
            // $table->string("membership_number")->after('customer_account_number')->nullable();
            // $table->string("decreasing_amount")->after('membership_number')->nullable();
            // $table->string("increasing_amount")->after("decreasing_amount")->nullable();
            // $table->text("over_paid")->after("increasing_amount")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cp_loans', function (Blueprint $table) {
            // Schema::dropIfExists('total_interest_paid');
            // Schema::dropIfExists('customer_account_number');
            // Schema::dropIfExists('decreasing_amount');
            // Schema::dropIfExists('increasing_amount');
        });
    }
};
