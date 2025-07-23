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
        Schema::create('room_details', function (Blueprint $table) {
        $table->id('room_details_id');
        $table->unsignedTinyInteger('subcategory_id');
        $table->string('room_code', 50);
        $table->unsignedTinyInteger('floor_level')->default(1); 
        $table->timestamps();

        // Foreign Key
        $table->foreign('subcategory_id')->references('subcategory_id')->on('facility_subcategories')->onDelete('restrict');
        $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_details');
    }
};
