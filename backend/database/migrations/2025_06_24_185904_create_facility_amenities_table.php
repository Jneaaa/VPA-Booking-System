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
        Schema::create('facility_amenities', function (Blueprint $table) {
            $table->id('facility_amenity_id');
            $table->unsignedBigInteger('facility_id');
            $table->string('amenity_name', 50);
            $table->decimal('amenity_fee', 10, 2)->nullable();
            $table->integer('quantity')->default(1);

            // Foreign Key
            $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_amenities');
    }
};
