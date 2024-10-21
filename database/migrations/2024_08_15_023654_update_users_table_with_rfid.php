<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rfid_id')->nullable()->constrained('rfids')->onDelete('cascade')->unique();
            $table->dropColumn('rfid'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rfid_id']);
            $table->dropColumn('rfid_id');
            $table->integer('rfid')->unique()->nullable(); 
        });
    }
};
