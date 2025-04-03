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
        Schema::table('users', function (Blueprint $table) {
            $table->string("otp_number")->nullable()->after("password");
            $table->dateTime('otp_expires_at')->nullable()->after("otp_number");
            $table->dateTime('last_login_at')->nullable()->after('otp_expires_at');
            $table->string("phone_number")->nullable();
            $table->string("address")->nullable();
            $table->string("city")->nullable();
            $table->string("state")->nullable();
            $table->string("country")->nullable();
            $table->enum('role', ['admin', 'user', 'editor'])->default('user');
            $table->boolean("is_verified")->default(true);
            $table->enum('status', ['enable', 'disable'])->default('enable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->dropColumn('otp_number');
            $table->dropColumn('otp_expires_at');
            $table->dropColumn('last_login_at');
            $table->dropColumn('phone_number');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('country');
            $table->dropColumn('role');
            $table->dropColumn('is_verified');
            $table->dropColumn('status');
        });
    }
};
