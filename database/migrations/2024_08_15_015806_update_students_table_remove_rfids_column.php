<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStudentsTableRemoveRfidsColumn extends Migration
{
    public function up()
{
    Schema::table('students', function (Blueprint $table) {
        // Drop the existing RFID column
        $table->dropColumn('rfid');

        // Add a new column to reference the RFID table
        $table->foreignId('rfid_id')->nullable()->constrained('rfids')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('students', function (Blueprint $table) {
        // Drop the foreign key and column
        $table->dropForeign(['rfid_id']);
        $table->dropColumn('rfid_id');

        // Add the RFID column back if needed
        $table->integer('rfid')->unique()->nullable();
    });
}

}
