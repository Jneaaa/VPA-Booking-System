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
        Schema::create('facility_images', function (Blueprint $table) {
            $table->id('image_id');
            $table->unsignedBigInteger('facility_id');
            $table->string('image_url')->default('https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp')->nullable;
            $table->string('cloudinary_public_id')->default('oxvsxogzu9koqhctnf7s')->nullable();
            $table->string('description', 80)->nullable();
            $table->integer('sort_order')->default(0);
            $table->enum('image_type', ['Primary','Secondary']);
            $table->timestamps();

            // Foreign Keys
            $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_images');
    }
};
