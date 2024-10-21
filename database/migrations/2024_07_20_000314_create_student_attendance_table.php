<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentAttendanceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->timestamp('entered_at')->nullable();
            $table->timestamp('exited_at')->nullable();
            $table->enum('status', ['Present', 'Absent', 'No Timeout']);
            $table->foreign('student_id')->references('id')->on('students')->onDelete('no action');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('no action');

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
        Schema::dropIfExists('student_attendances');
    }
}
