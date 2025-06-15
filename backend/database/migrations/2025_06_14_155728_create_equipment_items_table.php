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
        Schema::create('equipment_items', function (Blueprint $table) {
            $table->id('equipment_id');
            $table->string('item_name')->nullable();
            $table->unsignedTinyInteger('condition_id');
            $table->string('barcode_number')->nullable();
            $table->text('item_notes')->nullable();
            $table->timestamps();
            $table->foreign('condition_id')->references('condition_id')->on('conditions')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_items');
    }
};
