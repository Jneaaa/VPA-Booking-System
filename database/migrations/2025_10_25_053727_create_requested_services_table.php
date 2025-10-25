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
        Schema::create('requested_services', function (Blueprint $table) {
            $table->id('requested_service_id');

            // Foreign keys
            $table->unsignedBigInteger('request_id')->index();
            $table->unsignedBigInteger('service_id')->index();
            $table->unsignedInteger('quantity')->default(1);

            // Foreign key constraints
            $table->foreign('request_id')
                  ->references('request_id')
                  ->on('requisition_forms')
                  ->onDelete('cascade');

            $table->foreign('service_id')
                  ->references('service_id')
                  ->on('extra_services')
                  ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requested_services');
    }
};
