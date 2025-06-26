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
        Schema::create('purpose_discounts', function (Blueprint $table) {
            
            $table->id('rule_id');
            $table->unsignedTinyInteger('purpose_id')->unique()->index(); // FK to purposes
            $table->decimal('facility_discount_percent', 5, 2)->default(0.00);
            $table->decimal('equipment_discount_percent', 5, 2)->default(0.00);
            $table->boolean('is_free')->default(false); // full waiver
            $table->boolean('pay_led')->default(true); // must pay if with LED
            $table->boolean('pay_overtime')->default(true); // must pay if with OT
            $table->boolean('applies_only_if_with_fee')->default(false); // for those "with a fee"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purpose_discounts');
    }
};
