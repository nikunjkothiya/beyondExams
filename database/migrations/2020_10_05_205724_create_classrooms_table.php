<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassroomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classrooms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->dateTime('start_datetime')->default(Carbon::now());
            $table->unsignedBigInteger('timezone_id')->default(1);
            $table->integer('duration');
            $table->unsignedBigInteger('time_recursion_id')->default(1);
            $table->unsignedBigInteger('access_type_id')->default(2);
            $table->integer('max_students')->default(50);
            $table->integer('student_count')->default(0);
            $table->unsignedBigInteger('teacher_id');
            $table->timestamps();
        });

        Schema::table('classrooms', function (Blueprint $table) {
            $table->foreign('timezone_id')->references('id')->on('timezones');
            $table->foreign('time_recursion_id')->references('id')->on('time_recursion_types');
            $table->foreign('access_type_id')->references('id')->on('access_types');
            $table->foreign('teacher_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classrooms');
    }
}
