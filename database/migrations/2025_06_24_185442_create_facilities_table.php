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
            $table->unsignedBigInteger('parent_facility_id')->nullable();
            $table->unsignedBigInteger('room_details_id')->nullable(); // for indoor type facilities: fill up room code and what floor level its in. it also includes a Subcategory FK that points to its parent subcategory where it belongs to, so this field will only show up in the UI if the correct subcategory is selected (please validate if logic is correct)
            $table->unsignedBigInteger('building_details_id')->nullable(); // for building type facilities: fill up building code, total floor levels, and total rooms. same subcategory logic as room details.

            $table->string('facility_name', 50);
            $table->string('description', 250)->default('No description provided for this facility.');
            $table->unsignedInteger('maximum_rental_hour')->default(1);
            $table->unsignedTinyInteger('category_id')->default(1);
            $table->unsignedTinyInteger('subcategory_id')->nullable();
            $table->string('location_note', 200)->default('No location note provided.');
            $table->unsignedInteger('capacity')->default(1);
            $table->unsignedTinyInteger('department_id');
            $table->enum('location_type', ['Indoors', 'Outdoors']);
            $table->decimal('internal_fee', 10, 2);
            $table->decimal('external_fee', 10, 2);
            $table->enum('rate_type', ['Per Hour', 'Per Event'])->default('Per Hour');
            $table->unsignedTinyInteger('status_id');
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->dateTime('last_booked_at')->nullable();

            // Foreign key constraints 
            $table->foreign('parent_facility_id')->references('facility_id')->on('facilities')->onDelete('cascade'); 
            $table->foreign('building_details_id')->references('building_details_id')->on('building_details');
            $table->foreign('room_details_id')->references('room_details_id')->on('room_details');
            $table->foreign('category_id')->references('category_id')->on('facility_categories');
            $table->foreign('subcategory_id')->references('subcategory_id')->on('facility_subcategories');
            $table->foreign('department_id')->references('department_id')->on('departments');
            $table->foreign('status_id')->references('status_id')->on('availability_statuses');
            $table->foreign('created_by')->references('admin_id')->on('admins');
            $table->foreign('updated_by')->references('admin_id')->on('admins');
            $table->foreign('deleted_by')->references('admin_id')->on('admins');

            // Composite indexes for queries 
            $table->index(['category_id', 'subcategory_id']);
            $table->index(['parent_facility_id']);
            $table->index(['department_id', 'status_id']);

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
