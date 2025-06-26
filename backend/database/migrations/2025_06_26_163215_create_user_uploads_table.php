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
            $table->string('file_url');
            $table->string('cloudinary_public_id');
            $table->enum('upload_type', ['Letter', 'Room Setup'])->nullable()->index();
            $table->string('upload_token', 50)->nullable(); // used before form finalization
            $table->unsignedBigInteger('requisition_id')->nullable()->index(); // linked after final submit
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_uploads');
    }
};
