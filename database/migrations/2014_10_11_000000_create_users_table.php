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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->index();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('birthdate');
            $table->enum('role', ['admin','user'])->default('user');
            $table->foreignId('store_id')->nullable()->constrained('stores')->cascadeOnDelete('SET NULL');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
