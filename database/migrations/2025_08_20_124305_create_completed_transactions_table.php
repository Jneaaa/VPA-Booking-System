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
        Schema::create('completed_transactions', function (Blueprint $table) {
            $table->id('transaction_id');
            $table->unsignedBigInteger('request_id');
            $table->string('official_receipt_no', 50)->nullable();
            $table->string('official_receipt_url')->nullable();
            $table->string('official_receipt_public_id')->nullable();
            $table->timestamps();

            // foreign keys
            $table->foreign('request_id')->references('request_id')->on('requisition_forms');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('completed_transactions');
    }
};
