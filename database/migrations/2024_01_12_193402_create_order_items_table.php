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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->string('order_id',10);
            $table->date('date');
            $table->enum('status',['On Queue', 'In Transit', 'Finished'])->default("On Queue");
            $table->foreignId('store_id')->nullable()->constrained('stores')->cascadeOnDelete('SET NULL');
            $table->foreignId('full_fill_by')->constrained('stores')->cascadeOnDelete('SET NULL');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete('SET NULL');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
