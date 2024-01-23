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
        Schema::create('items', function (Blueprint $table) {
            $table->id()->index();
            $table->string('barcode', 100)->nullable();
            $table->string('name');
            $table->enum('category',['Packaging','Raw Material','Fresh Milk']); // Packaging, Raw Material, Fresh Milk
            $table->enum('unit',['Pack','GR','ML']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
