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
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('admin_id');
            $table->dateTime('date_approved');
            $table->timestamps();

            // Foreign Keys
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
