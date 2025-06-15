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
        Schema::create('equipment_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->foreignId('equipment_id');
            $table->string('image_url', 500);
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->unsignedtinyInteger('type_id')->default(1); // Assuming 1 is the default type_id for 'primary' images
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('type_id')->references('type_id')->on('image_types')->onDelete('restrict');
            $table->foreign('equipment_id')->references('equipment_id')->on('equipment')->onDelete('cascade');

            
            // Indexes for performance
            $table->index(['equipment_id', 'sort_order']);
            $table->index(['equipment_id', 'type_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_images');
    }
};
