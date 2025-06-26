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
            
            // Foreign Keys
            $table->unsignedBigInteger('equipment_id');
            $table->unsignedTinyInteger('action_id')->nullable(); // Action type, e.g., maintenance, repair, etc.
            
            // Fee tracking
            $table->decimal('fee_before', 10, 2)->nullable();
            $table->decimal('fee_after', 10, 2)->nullable();
        
            // Audit tracking
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            
            // Booking timestamp
            $table->dateTime('last_booked')->nullable(); // use dateTime, not timestamp, for wider range
        
            $table->timestamps(); // created_at, updated_at
            $table->softDeletes(); // includes deleted_at

            // Indexes
            $table->index('equipment_id');
            $table->index('action_id');
        
            // Foreign Key Constraints
            $table->foreign('equipment_id')->references('equipment_id')->on('equipment')->onDelete('cascade');
            $table->foreign('action_id')->references('action_id')->on('action_types')->onDelete('set null');
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
        Schema::dropIfExists('equipment_logs');
    }
};
