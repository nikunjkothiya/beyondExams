<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_documents', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('timetable_id');
            $table->unsignedBigInteger('creator_id');
            $table->string('document_name')->nullable();
            $table->string('document_path')->nullable();
            $table->enum('type', ['1','2','3'])->nullable()->comment('1=test,2=resource,3=homework');
            $table->timestamps();
        });

        Schema::table('teacher_documents',function($table){
            $table->foreign('timetable_id')->references('id')->on('timetables')->onDelete('cascade');
            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_documents');
    }
}
