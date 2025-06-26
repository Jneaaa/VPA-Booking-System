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
        Schema::create('admins', function (Blueprint $table) {
            $table->id('admin_id')->autoIncrement();
            $table->string('photo_url', 500)->nullable();
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('middle_name', 50)->nullable();
            $table->unsignedTinyInteger('role_id');
            $table->string('school_id', 20)->nullable();
            $table->string('email', 150)->unique();
            $table->string('contact_number', 20)->nullable();
            $table->string('hashed_password', 100);
            $table->timestamps();
         
            // Foreign key constraint
            $table->foreign('role_id')
                ->references('role_id')
                ->on('admin_roles')
                ->onDelete('cascade');

            $table->index('role_id');
         });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};
