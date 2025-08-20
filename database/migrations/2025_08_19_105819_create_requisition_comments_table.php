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
        Schema::create('requisition_comments', function (Blueprint $table) {
            $table->id('comment_id');
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('admin_id');
            $table->text('comment')->nullable();
            $table->timestamps();

            // foreign keys
            $table->foreign('request_id')->references('request_id')->on('requisition_forms')->onDelete('cascade');
            $table->foreign('admin_id')->references('admin_id')->on('admins')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_comments');
    }
};
