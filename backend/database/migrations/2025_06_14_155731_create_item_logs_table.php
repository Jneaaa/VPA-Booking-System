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
        Schema::create('item_logs', function (Blueprint $table) {
            $table->id('log_id');
            $table->unsignedBigInteger('item_id');
            $table->unsignedTinyInteger('action_id')->nullable();
            $table->unsignedTinyInteger('condition_before')->nullable();
            $table->unsignedTinyInteger('condition_after')->nullable();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
        
            $table->foreign('item_id')->references('equipment_id')->on('equipment_items')->onDelete('cascade');
            $table->foreign('action_id')->references('action_id')->on('action_types')->onDelete('set null');
            $table->foreign('condition_before')->references('condition_id')->on('conditions')->onDelete('set null');
            $table->foreign('condition_after')->references('condition_id')->on('conditions')->onDelete('set null');
            $table->foreign('created_by')->references('admin_id')->on('admins')->onDelete('restrict');
            $table->foreign('updated_by')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('deleted_by')->references('admin_id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_logs');
    }
};
