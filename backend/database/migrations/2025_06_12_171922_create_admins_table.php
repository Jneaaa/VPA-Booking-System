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
            $table->string('username')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('middle_name')->nullable();
            $table->unsignedTinyInteger('role_id');
            $table->string('school_id')->nullable();
            $table->string('email')->unique();
            $table->string('contact_number')->nullable();
            $table->string('hashed_password');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('role_id')
            ->references('role_id')
            ->on('admin_roles')
            ->onDelete('cascade');
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
