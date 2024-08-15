<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('student_number')->unique();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->enum('program', ['BSIT', 'BLIS', 'BSCS', 'BSIS']);
            $table->enum('year', [1, 2, 3, 4]);
            $table->enum('section', ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H']);
            $table->enum('gender', ['male', 'female']);
            $table->integer('pc_number');
            $table->integer('rfid')->unique()->nullable();
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
        Schema::dropIfExists('students');
    }
}
