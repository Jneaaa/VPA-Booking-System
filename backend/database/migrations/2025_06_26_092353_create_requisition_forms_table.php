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
            $table->unsignedTinyInteger('status_id')->index();

            // booking schedule
            $table->date('start_date');
            $table->date('end_date');
            $table->time('start_time');
            $table->time('end_time');

            // for late returns
            $table->boolean('is_late')->default(false);
            $table->decimal('late_penalty_fee', 10, 2)->nullable();
            $table->timestamp('returned_at')->nullable();

            // finalization
            $table->boolean('is_finalized')->default(false);
            $table->timestamp('finalized_at')->nullable();
            $table->unsignedBigInteger('finalized_by')->nullable()->index();

            // close transaction
            $table->boolean('is_closed')->default(false);
            $table->timestamp('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable()->index();

            // endorsement
            $table->string('endorser', 50)->nullable();
            $table->date('date_endorsed')->nullable();
            $table->timestamps();

            // Foreign Keys
            $table->foreign('finalized_by')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('closed_by')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('user_id')->references('user_id')->on('users')->onDelete('cascade');
            $table->foreign('purpose_id')->references('purpose_id')->on('requisition_purposes')->onDelete('restrict');
            $table->foreign('status_id')->references('status_id')->on('form_statuses')->onDelete('restrict');
           
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requisition_forms');
    }
};
