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
            $table->unsignedTinyInteger('type_id')->nullable();
            $table->integer('sort_order')->nullable();
            $table->string('cloudinary_public_id')->nullable();
            $table->string('image_url')->nullable();

            // Foreign Keys
            $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
            $table->foreign('type_id')->references('type_id')->on('image_types'); // adjust if different

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
