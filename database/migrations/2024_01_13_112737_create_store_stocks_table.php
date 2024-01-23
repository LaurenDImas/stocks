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
        Schema::create('store_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete('SET NULL');
            $table->foreignId('order_item_detail_id')->constrained('order_item_details')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete('SET NULL');
            $table->date('arrived_date');
            $table->bigInteger('stock')->default(0); // Packaging, Raw Material, Fresh Milk
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('store_stocks');
    }
};
