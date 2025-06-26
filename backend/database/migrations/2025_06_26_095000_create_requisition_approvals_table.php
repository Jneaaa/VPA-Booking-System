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
        Schema::create('requisition_approvals', function (Blueprint $table) {
            
            $table->id('approval_id');
            $table->enum('status_id', ['pending', 'approved', 'rejected'])->default('pending')->index();
            $table->unsignedBigInteger('request_id')->index();
            $table->unsignedBigInteger('admin_id')->index();
            $table->dateTime('date_approved');
            $table->boolean('is_finalizer')->default(false);


            $table->foreign('request_id')->references('request_id')->on('requisition_forms')->onDelete('cascade');
            $table->foreign('admin_id')->references('admin_id')->on('admins')->onDelete('cascade');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_approvals');
    }
};
