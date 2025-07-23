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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id('equipment_id');
            $table->string('equipment_name', 50);
            $table->string('description', 255)->default('No description provided for this equipment.');
            $table->string('brand', 80)->default('Not applicable.');
            $table->string('storage_location', 50)->default('No storage location specified.');
            $table->unsignedTinyInteger('category_id');
            $table->unsignedInteger('total_quantity')->default(1);
            $table->decimal('internal_fee', 10, 2);
            $table->decimal('external_fee', 10, 2);
            $table->enum('rate_type', ['Per Hour', 'Per Event'])->default('Per Hour');
            $table->unsignedTinyInteger('status_id');
            $table->unsignedTinyInteger('department_id');
            $table->unsignedInteger('maximum_rental_hour')->default(1);
            $table->timestamps();
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->dateTime('last_booked_at')->nullable();
        
            // Foreign key constraints
            $table->foreign('created_by')->references('admin_id')->on('admins')->onDelete('restrict');
            $table->foreign('updated_by')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('deleted_by')->references('admin_id')->on('admins')->onDelete('set null');

            $table->foreign('category_id')->references('category_id')->on('equipment_categories')->onDelete('restrict');
            $table->foreign('status_id')->references('status_id')->on('availability_statuses')->onDelete('restrict');
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('restrict');
        
            // Indexes for query optimization
            $table->index('equipment_name', 'idx_equipment_name');
            $table->index('brand', 'idx_equipment_brand');
            $table->index('storage_location', 'idx_equipment_storage_location');
            $table->index('category_id', 'idx_equipment_category');
            $table->index('status_id', 'idx_equipment_status');
            $table->index('department_id', 'idx_equipment_department');
            
            // Composite indexes for common query patterns
            $table->index(['category_id', 'status_id'], 'idx_equipment_category_status');
            $table->index(['department_id', 'status_id'], 'idx_equipment_dept_status');
            $table->index(['status_id', 'category_id'], 'idx_equipment_status_category');
            $table->index(['internal_fee', 'status_id'], 'idx_equipment_fee_status');
            
            // Index for availability and quantity queries
            $table->index(['status_id', 'total_quantity'], 'idx_equipment_availability');
            
            // Text search optimization (if using MySQL/PostgreSQL full-text search)
            // $table->fullText(['equipment_name', 'description'], 'idx_equipment_fulltext');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
