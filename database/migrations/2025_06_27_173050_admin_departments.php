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
        Schema::create('admin_departments', function (Blueprint $table) {
            $table->unsignedBigInteger('admin_id');
            $table->unsignedTinyInteger('department_id');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            // Composite Primary Key
            $table->primary(['admin_id', 'department_id']);

            // Foreign Keys
            $table->foreign('admin_id')->references('admin_id')->on('admins')->onDelete('cascade');
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admin_departments');
    }
};
