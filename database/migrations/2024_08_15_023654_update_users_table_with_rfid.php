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
        // Add the foreign key column to 'users'
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('rfid_id')->nullable()->constrained('rfids')->onDelete('set null');
            $table->dropColumn('rfid'); // Optionally, drop the old column if it's no longer needed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the foreign key and restore the old column
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['rfid_id']);
            $table->dropColumn('rfid_id');
            $table->integer('rfid')->unique()->nullable(); // Re-add the old column if necessary
        });
    }
};
