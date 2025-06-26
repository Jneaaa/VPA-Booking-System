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
        Schema::create('feedback_forms', function (Blueprint $table) {

            $table->id('feedback_id');
            // PK, AI
            $table->string('email', 100)->index(); 
            $table->string('first_name', 50)->nullable();
            $table->string('last_name', 50)->nullable();
    
            $table->unsignedBigInteger('request_id')->nullable()->index(); 
    
            // Ratings as ENUM (1 to 5)
            $table->enum('performance_rating', ['1', '2', '3', '4', '5'])->index();
            $table->enum('booking_satisfaction', ['1', '2', '3', '4', '5']);
            $table->enum('ease_of_use', ['1', '2', '3', '4', '5']);
            $table->enum('reuse_likelihood', ['1', '2', '3', '4', '5']);
    
            $table->string('open_feedback', 500)->nullable(); 
    
            $table->timestamps(); 
    

            $table->foreign('request_id')->references('request_id')->on('requisition_forms')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_forms');
    }
};
