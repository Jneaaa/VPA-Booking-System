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
        Schema::create('equipment_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('equipment_id')->notNullable();
            $table->unsignedTinyInteger('action_type')->nullable();
            $table->decimal('fee_before', 10, 2)->nullable();
            $table->decimal('fee_after', 10, 2)->nullable();
            $table->dateTime('last_booked')->nullable();
            $table->unsignedBigInteger('created_by')->notNullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();
        
            $table->foreign('equipment_id')->references('equipment_id')->on('equipment_items')->onDelete('cascade');
            $table->foreign('action_type')->references('type_id')->on('action_types')->onDelete('set null');
            $table->foreign('created_by')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('updated_by')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('deleted_by')->references('admin_id')->on('admins')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_logs');
    }
};
