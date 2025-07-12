<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAttendanceCorrectionRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('attendance_correction_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('attendance_id')->constrained()->cascadeOnDelete();
            $table->foreignId('break_id')->nullable()->constrained()->cascadeOnDelete();
            $table->time('requested_start_time')->nullable();
            $table->time('requested_end_time')->nullable();
            $table->time('requested_break1_start_time')->nullable();
            $table->time('requested_break1_end_time')->nullable();
            $table->time('requested_break2_start_time')->nullable();
            $table->time('requested_break2_end_time')->nullable();
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('pending: 承認待ち, approved: 承認済, rejected: 却下');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('attendance_correction_requests');
    }
}
