<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStudentsTableRemoveRfidsColumn extends Migration
{
    public function up()
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropColumn('rfid');

        $table->foreignId('rfid_id')->nullable()->constrained('rfids')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('students', function (Blueprint $table) {
        $table->dropForeign(['rfid_id']);
        $table->dropColumn('rfid_id');

        $table->integer('rfid')->unique()->nullable();
    });
}

}
