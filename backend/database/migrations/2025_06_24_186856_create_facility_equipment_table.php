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
        Schema::create('facility_equipment', function (Blueprint $table) {
            $table->id('facility_equipment_id');
            $table->unsignedBigInteger('facility_id');
            $table->unsignedBigInteger('equipment_id');
            $table->unsignedBigInteger('quantity')->default(1);
            
            // Foreign Keys
            $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
            $table->foreign('equipment_id')->references('equipment_id')->on('equipment')->onDelete('cascade'); // adjust 'equipment' table name if different
            
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_equipment');
    }
};
