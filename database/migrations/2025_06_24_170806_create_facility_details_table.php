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
        Schema::create('facility_details', function (Blueprint $table) {
        $table->id('detail_id');
        $table->unsignedTinyInteger('subcategory_id');
        $table->string('room_name', 50)->nullable();
        $table->string('building_name', 50)->nullable();
        $table->string('building_code', 10)->nullable();
        $table->string('room_number', 10)->nullable();
        $table->unsignedTinyInteger('floor_level');
        $table->timestamps();

// Foreign Key
$table->foreign('subcategory_id')->references('subcategory_id')->on('facility_subcategories')->onDelete('cascade');

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
