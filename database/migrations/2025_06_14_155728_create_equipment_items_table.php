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
            $table->string('item_name', 50);
            $table->string('image_url')->default('https://res.cloudinary.com/dn98ntlkd/image/upload/v1750895337/oxvsxogzu9koqhctnf7s.webp');
            $table->string('cloudinary_public_id')->default('oxvsxogzu9koqhctnf7s');
            $table->unsignedTinyInteger('status_id');
            $table->unsignedTinyInteger('condition_id');
            $table->string('barcode_number', 20)->nullable();
            $table->string('item_notes', 100)->default('No notes provided for this asset.');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->timestamps();

            // Foreign Key Constraints
            $table->foreign('status_id')->references('status_id')->on('availability_statuses')->onDelete('restrict');
            $table->foreign('condition_id')->references('condition_id')->on('conditions')->onDelete('restrict');
            $table->foreign('equipment_id')->references('equipment_id')->on('equipment')->onDelete('cascade');
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
        Schema::dropIfExists('equipment_items');
    }
};
