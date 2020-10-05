<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CreateClassesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedBigInteger('grade_id')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->date('class_start_date')->default(Carbon::now());
            $table->time('class_start_time')->default(Carbon::now());
            $table->integer('duration')->default(60);
            $table->unsignedBigInteger('time_recursion_id');
            $table->unsignedBigInteger('access_type_id')->default(2);
            $table->integer('max_students')->default(50);
            $table->timestamps();
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->foreign('time_recursion_id')->references('id')->on('time_recursion_types');
            $table->foreign('access_type_id')->references('id')->on('access_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('classes');
    }
}
