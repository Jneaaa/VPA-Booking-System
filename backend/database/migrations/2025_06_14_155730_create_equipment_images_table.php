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
            $table->string('image_url')->default('https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp')->nullable;
            $table->string('cloudinary_public_id')->default('oxvsxogzu9koqhctnf7s')->nullable();
            $table->string('description', 80)->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('image_type', ['Primary','Secondary']);
            $table->timestamps();
            
            // Foreign key constraints
            $table->foreign('equipment_id')->references('equipment_id')->on('equipment')->onDelete('cascade');
   
            // Indexes for performance
            $table->index(['equipment_id', 'sort_order']);

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
