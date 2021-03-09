<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTimetableHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('timetable_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedBigInteger('teacher_id');
            $table->string('time');
            $table->timestamps();
        });

        Schema::table('timetable_history',function($table){
            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('timetable_history');
    }
}
