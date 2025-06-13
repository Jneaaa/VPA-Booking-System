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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id('equipment_id');
            $table->string('equipment_name', 50)->notNullable();
            $table->string('description', 255)->nullable();
            $table->string('brand', 80)->nullable();
            $table->string('storage_location', 50)->notNullable();
            $table->unsignedTinyInteger('category_id')->notNullable();
            $table->integer('total_quantity')->notNullable();
            $table->decimal('rental_fee', 10, 2)->notNullable();
            $table->decimal('company_fee', 10, 2)->notNullable();
            $table->unsignedTinyInteger('type_id')->notNullable();
            $table->unsignedTinyInteger('status_id')->notNullable();
            $table->unsignedTinyInteger('department_id')->notNullable();
            $table->integer('minimum_hour')->notNullable();
            $table->timestamps();
        
            $table->foreign('category_id')->references('category_id')->on('equipment_categories')->onDelete('restrict');
            $table->foreign('type_id')->references('type_id')->on('rate_types')->onDelete('restrict');
            $table->foreign('status_id')->references('status_id')->on('availability_statuses')->onDelete('restrict');
            $table->foreign('department_id')->references('department_id')->on('departments')->onDelete('restrict');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
