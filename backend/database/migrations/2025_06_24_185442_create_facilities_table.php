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
        Schema::create('facilities', function (Blueprint $table) {
            $table->id('facility_id');
            $table->string('facility_name', 50);
            $table->string('description', 250)->nullable();
            
            $table->unsignedTinyInteger('category_id');
            $table->unsignedTinyInteger('subcategory_id')->nullable();
            $table->unsignedBigInteger('room_id')->nullable();
            
            $table->string('location_note', 200);
            $table->unsignedInteger('capacity');
            
            $table->unsignedTinyInteger('department_id');
            $table->enum('is_indoors', ['Indoors', 'Outdoors']);
            
            $table->decimal('rental_fee', 10, 2);
            $table->decimal('company_fee', 10, 2);
            
            $table->unsignedTinyInteger('status_id');

            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->dateTime('last_booked_at')->nullable();

            // Foreign key constraints (optional, remove if you don’t want FK checks here)
            $table->foreign('category_id')->references('category_id')->on('facility_categories');
            $table->foreign('subcategory_id')->references('subcategory_id')->on('facility_subcategories');
            $table->foreign('room_id')->references('room_id')->on('room_details');
            $table->foreign('department_id')->references('department_id')->on('departments');
            $table->foreign('status_id')->references('status_id')->on('availability_statuses');
            $table->foreign('created_by')->references('admin_id')->on('admins');
            $table->foreign('updated_by')->references('admin_id')->on('admins');
            $table->foreign('deleted_by')->references('admin_id')->on('admins');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facilities');
    }
};
