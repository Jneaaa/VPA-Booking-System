<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id('notification_id');
            $table->foreignId('admin_id')->constrained('admins', 'admin_id');
            $table->string('type'); // 'new_requisition', 'status_update', etc.
            $table->text('message');
            $table->foreignId('request_id')->nullable()->constrained('requisition_forms', 'request_id');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};