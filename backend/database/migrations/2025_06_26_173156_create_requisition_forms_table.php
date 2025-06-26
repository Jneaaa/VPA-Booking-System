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
        Schema::create('requisition_forms', function (Blueprint $table) {
            $table->id('request_id');
            $table->unsignedBigInteger('user_id')->index();
            $table->string('access_code', 10);
            $table->integer('num_participants');
            $table->unsignedTinyInteger('purpose_id')->index();
            $table->string('other_purpose', 250)->nullable();
            $table->string('additional_requests', 250)->nullable();
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedTinyInteger('status_id')->index();

            $table->decimal('late_penalty_fee', 10, 2)->nullable();

            // new fields
            $table->boolean('is_late')->default(false);
            $table->timestamp('returned_at')->nullable();
            $table->boolean('is_closed')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();

            // finalization
            $table->boolean('is_finalized')->default(false);
            $table->timestamp('finalized_at')->nullable();
            $table->unsignedBigInteger('finalized_by')->nullable();

            // endorsement
            $table->string('endorser', 50)->nullable();
            $table->date('date_endorsed')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('purpose_id')->references('purpose_id')->on('requisition_purposes')->onDelete('restrict');
            $table->foreign('status_id')->references('status_id')->on('form_status_codes')->onDelete('restrict');
            $table->foreign('finalized_by')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('closed_by')->references('admin_id')->on('admins')->onDelete('set null');
        });
    }
};

