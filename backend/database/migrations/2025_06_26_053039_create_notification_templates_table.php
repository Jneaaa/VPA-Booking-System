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
        Schema::create('notification_templates', function (Blueprint $table) {

            $table->id('template_id');
            $table->string('notification_name', 50); 
            $table->string('subject_template', 50); 
            $table->text('body_template'); 
    
            $table->timestamps(); 
    
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->unsignedBigInteger('deleted_by')->nullable()->index();
    
            // Add foreign key constraints
            $table->foreign('created_by')->references('admin_id')->on('admins')->nullOnDelete();
            $table->foreign('updated_by')->references('admin_id')->on('admins')->nullOnDelete();
            $table->foreign('deleted_by')->references('admin_id')->on('admins')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
