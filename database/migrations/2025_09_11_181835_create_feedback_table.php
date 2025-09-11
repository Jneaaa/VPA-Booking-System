<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('feedback', function (Blueprint $table) {
            $table->id('feedback_id');

            // Optional email
            $table->string('email')->nullable();

            // Link to requisition_forms
            $table->unsignedBigInteger('request_id')->nullable();
            $table->foreign('request_id')
                ->references('request_id')
                ->on('requisition_forms')
                ->onDelete('cascade');   // delete feedback if requisition form is deleted

            $table->enum('system_performance', [
                'poor',
                'fair',
                'satisfactory',
                'very good',
                'outstanding'
            ]);

            $table->enum('booking_experience', [
                'poor',
                'fair',
                'good',
                'very good',
                'excellent'
            ]);

            $table->enum('ease_of_use', [
                'very difficult',
                'difficult',
                'neutral',
                'easy',
                'very easy'
            ]);

            $table->enum('useability', [
                'very unlikely',
                'unlikely',
                'neutral',
                'likely',
                'very likely'
            ]);

            $table->text('additional_feedback')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback');
    }
};
