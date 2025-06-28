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
        Schema::create('facility_subcategories', function (Blueprint $table) {
            $table->tinyIncrements('subcategory_id');
            $table->unsignedTinyInteger('category_id');
            $table->string('subcategory_name', 50);
            $table->foreign('category_id')->references('category_id')->on('facility_categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_subcategories');
    }
};
