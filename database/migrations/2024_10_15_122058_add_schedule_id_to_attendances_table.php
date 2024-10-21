<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScheduleIdToAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
{
    Schema::table('attendances', function (Blueprint $table) {
        $table->unsignedBigInteger('schedule_id')->nullable()->after('faculty_id');
        $table->foreign('schedule_id')->references('id')->on('schedules')->onDelete('no action');
    });
}

public function down()
{
    Schema::table('attendances', function (Blueprint $table) {
        $table->dropForeign(['schedule_id']);
        $table->dropColumn('schedule_id');
    });
}

}
