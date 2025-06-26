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
        Schema::create('requisition_misc_fees', function (Blueprint $table) {
            
            $table->id('fee_id');
            $table->unsignedBigInteger('request_id')->index();

            // Custom label for the fee
            $table->string('charge_label', 50);

            // Waives the entire fee
            $table->boolean('is_waived')->default(false);

            // Fee Configurations
            $table->decimal('additional_fee', 10, 2)->nullable();
            $table->decimal('discount_fee', 10, 2)->nullable();


            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();
            
            $table->timestamps();
            
            $table->foreign('request_id')->references('request_id')->on('requisition_forms')->onDelete('cascade');
            $table->foreign('created_by')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('updated_by')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('deleted_by')->references('admin_id')->on('admins')->onDelete('set null');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_misc_fees');
    }
};
