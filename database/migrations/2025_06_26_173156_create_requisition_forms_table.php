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
            $table->enum('user_type', ['Internal', 'External'])->default('External');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email', 100);
            $table->string('school_id', 20)->nullable();
            $table->string('organization_name', 100)->nullable();
            $table->string('contact_number', 15)->nullable();
            ///////
            $table->string('access_code', 10);
            $table->integer('num_participants');
            $table->unsignedTinyInteger('purpose_id')->index();
            $table->string('additional_requests', 250)->nullable();
            $table->string('formal_letter_url');
            $table->string('formal_letter_public_id');
            $table->string('facility_layout_url')->nullable();
            $table->string('facility_layout_public_id')->nullable();
            $table->string('upload_token', 100)->nullable();
            $table->unsignedTinyInteger('status_id')->index();

            // booking schedule 
            $table->date('start_date')->index();
            $table->date('end_date')->index();
            $table->time('start_time');
            $table->time('end_time');

            // late returns 
            $table->decimal('late_penalty_fee', 10, 2)->nullable();
            $table->boolean('is_late')->default(false);
            $table->dateTime('returned_at')->nullable();

            // finalization
            $table->boolean('is_finalized')->default(false);
            $table->dateTime('finalized_at')->nullable();
            $table->unsignedBigInteger('finalized_by')->nullable();
            $table->string('official_receipt_no', 50)->nullable();
            $table->string('official_receipt_url')->nullable();
            $table->string('official_receipt_public_id')->nullable();

            // fees set by admins
            $table->decimal('tentative_fee', 10, 2)->nullable();
            $table->decimal('approved_fee', 10, 2)->nullable();

            // close form
            $table->boolean('is_closed')->default(false);
            $table->dateTime('closed_at')->nullable();
            $table->unsignedBigInteger('closed_by')->nullable();

            // endorsement
            $table->string('endorser', 50)->nullable();
            $table->dateTime('date_endorsed')->nullable();
            $table->timestamps();

            // calendar event details
            $table->string('calendar_title', 50)->default('Rental Request');
            $table->string('calendar_description', 100)->default('Rental request for facility usage');

            // Foreign Keys
            $table->foreign('purpose_id')->references('purpose_id')->on('requisition_purposes')->onDelete('restrict');
            $table->foreign('status_id')->references('status_id')->on('form_statuses')->onDelete('restrict');
            $table->foreign('finalized_by')->references('admin_id')->on('admins')->onDelete('set null');
            $table->foreign('closed_by')->references('admin_id')->on('admins')->onDelete('set null');
        });
    }
};

