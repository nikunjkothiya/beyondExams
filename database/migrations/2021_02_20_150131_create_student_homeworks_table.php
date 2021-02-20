<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudentHomeworksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_homeworks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedBigInteger('student_id');
            $table->string('document_name')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });

        Schema::table('student_homeworks',function($table){
            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_homeworks');
    }
}
