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
        Schema::create('equipment_items', function (Blueprint $table) {
            $table->id('item_id');
            $table->unsignedBigInteger('equipment_id');
            $table->string('item_name')->nullable();
            $table->unsignedTinyInteger('condition_id');
            $table->string('barcode_number')->nullable();
            $table->text('item_notes')->nullable();
            $table->timestamps();
            $table->foreign('condition_id')->references('condition_id')->on('conditions')->onDelete('restrict');
            $table->foreign('equipment_id')->references('equipment_id')->on('equipment')->onDelete('cascade');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();

            // Foreign Key Constraints
            $table->foreign('created_by')->references('admin_id')->on('admins')->onDelete('restrict');
            $table->foreign('updated_by')->references('admin_id')->on('admins')->onDelete('set null');

            // Indexes
            $table->index('equipment_id');
            $table->index('condition_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_items');
    }
};
