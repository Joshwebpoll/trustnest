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
        Schema::create('cp_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('membership_number')->unique();
            $table->string('full_name');
            $table->string('id_number')->unique();
            $table->string('phone');
            $table->string('email')->unique();
            $table->date('joining_date');
            $table->enum('status', ['active', 'inactive', 'suspended']);
            $table->text('total_shares');
            $table->text('total_savings');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cp_members');
    }
};
