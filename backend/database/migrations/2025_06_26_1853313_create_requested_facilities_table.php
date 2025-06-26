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
        Schema::create('requested_facilities', function (Blueprint $table) {
            $table->id('requested_facility_id');
            $table->unsignedBigInteger('request_id')->index();
            $table->unsignedBigInteger('facility_id')->index();
            $table->boolean('is_waived')->default(false);
        
            $table->foreign('request_id')->references('request_id')->on('requisition_forms')->onDelete('cascade');
            $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requested_facilities');
    }
};
