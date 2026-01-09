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
        Schema::create('requisition_fees', function (Blueprint $table) {

            // add fee or discount to request form
            $table->id('fee_id');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('added_by');
            $table->string('label', 50);
            $table->string('account_num', 10)->nullable();
            $table->decimal('fee_amount', 10, 2)->default(0.00);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->enum('discount_type', ['Fixed', 'Percentage'])->nullable();

            // timestamps
            $table->timestamps();

            // Foreign Keys
            $table->foreign('request_id')->references('request_id')->on('requisition_forms')->onDelete('cascade');
            $table->foreign('added_by')->references('admin_id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_fees');
    }
};
