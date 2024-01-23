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
        Schema::create('stock_opnames', function (Blueprint $table) {
            $table->bigIncrements('id')->index();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete('SET NULL');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete('SET NULL');
            $table->date('date');
            $table->bigInteger('qty')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_opnames');
    }
};
