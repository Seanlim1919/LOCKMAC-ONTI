<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('faculty_id')->nullable();
            $table->unsignedBigInteger('course_id')->nullable();
            $table->string('course_code');
            $table->string('course_name');
            $table->string('day');
            $table->enum('program', ['BSIT', 'BLIS', 'BSCS', 'BSIS']);
            $table->enum('year', [1, 2, 3, 4]);
            $table->enum('section', ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']);
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedBigInteger('semester_id');
            $table->integer('status')->default(0);

            $table->timestamps();
            
            $table->foreign('faculty_id')->references('id')->on('users')->onDelete('no action');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('no action');
            $table->foreign('semester_id')->references('id')->on('semester')->onDelete('no action');
        });
        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('schedules');
    }
}
