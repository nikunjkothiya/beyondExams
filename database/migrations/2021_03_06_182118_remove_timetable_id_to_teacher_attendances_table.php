<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTimetableIdToTeacherAttendancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teacher_attendances', function (Blueprint $table) {
            $table->dropForeign(['timetable_id']);
            $table->dropColumn('timetable_id');
            $table->unsignedBigInteger('chat_id')->after('id');
        });

        Schema::table('teacher_attendances',function($table){
            $table->foreign('chat_id')->references('id')->on('chats')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_attendances', function (Blueprint $table) {
            //
        });
    }
}
