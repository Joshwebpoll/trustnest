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
            $table->string('surname')->nullable()->after('name');
            $table->string('lastname')->nullable()->after("surname");
            $table->string('nin')->nullable();
            $table->string("bvn")->nullable();
            $table->string("gender")->nullable();
            $table->date('date_of_birth')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('surname');
            $table->dropColumn('lastname');
            $table->dropColumn('nin');
            $table->dropColumn('bvn');
            $table->dropColumn('gender');
            $table->dropColumn('date_of_birth');
        });
    }
};
