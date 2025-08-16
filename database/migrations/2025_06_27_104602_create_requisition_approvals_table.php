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
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('approved_by')->nullable();
            $table->unsignedBigInteger('rejected_by')->nullable();
            $table->string('remarks', 255)->nullable();
            $table->dateTime('date_posted')->useCurrent();

            // Foreign Keys
            $table->foreign('request_id')->references('request_id')->on('requisition_forms')->onDelete('cascade');
            $table->foreign('approved_by')->references('admin_id')->on('admins')->onDelete('cascade');
            $table->foreign('rejected_by')->references('admin_id')->on('admins')->onDelete('cascade');

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
