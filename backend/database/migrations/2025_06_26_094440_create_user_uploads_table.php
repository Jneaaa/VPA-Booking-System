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
        Schema::create('user_uploads', function (Blueprint $table) {
            $table->id('upload_id');
            $table->unsignedBigInteger('request_id')->index();
            $table->string('file_url');
            $table->string('cloudinary_public_id');
            $table->enum('upload_type', ['Letter', 'Room Setup'])->index();
            $table->string('upload_token', 50)->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('request_id')->on('requisition_forms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_files');
    }
};
