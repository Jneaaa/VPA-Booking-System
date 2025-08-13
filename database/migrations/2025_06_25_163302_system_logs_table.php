<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('system_logs', function (Blueprint $table) {
            $table->id('log_id');

            $table->unsignedTinyInteger('action_id');
            $table->unsignedBigInteger('equipment_id')->nullable();
            $table->unsignedBigInteger('item_id')->nullable();
            $table->unsignedBigInteger('facility_id')->nullable();

            // Fee tracking
            $table->decimal('fee_before', 10, 2)->nullable();
            $table->decimal('fee_after', 10, 2)->nullable();

            // Condition tracking
            $table->unsignedTinyInteger('condition_before')->nullable();
            $table->unsignedTinyInteger('condition_after')->nullable();

            // Audit tracking
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            // Foreign Key Constraints
            $table->foreign('equipment_id')->references('equipment_id')->on('equipment')->onDelete('cascade');
            $table->foreign('item_id')->references('item_id')->on('equipment_items')->onDelete('cascade');
            $table->foreign('facility_id')->references('facility_id')->on('facilities')->onDelete('cascade');
            $table->foreign('action_id')->references('action_id')->on('action_types')->onDelete('cascade');
            $table->foreign('created_by')->references('admin_id')->on('admins')->onDelete('restrict');
            $table->foreign('updated_by')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('deleted_by')->references('admin_id')->on('admins')->onDelete('set null');
             $table->foreign('condition_before')->references('condition_id')->on('conditions')->onDelete('set null');
            $table->foreign('condition_after')->references('condition_id')->on('conditions')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('system_logs');
    }
};
