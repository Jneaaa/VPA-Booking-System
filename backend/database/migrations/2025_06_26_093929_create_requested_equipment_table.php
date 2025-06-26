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
        Schema::create('requested_equipment', function (Blueprint $table) {
            $table->id('requested_equipment_id');
            $table->unsignedBigInteger('request_id')->index();
            $table->unsignedBigInteger('equipment_id')->index();
            $table->boolean('is_waived')->default(0);
            $table->timestamps();
            
            $table->foreign('request_id')->references('request_id')->on('requisition_forms')->onDelete('cascade');
            $table->foreign('equipment_id')->references('equipment_id')->on('equipment')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requested_equipment');
    }
};
