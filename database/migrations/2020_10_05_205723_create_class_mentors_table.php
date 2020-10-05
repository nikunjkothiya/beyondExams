<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClassMentorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('class_mentors', function (Blueprint $table) {
            $table->unsignedBigInteger('class_id')->nullable();
            $table->unsignedBigInteger('mentor_id')->nullable();
        });

        Schema::table('class_mentors', function (Blueprint $table) {
            $table->foreign('class_id')->references('id')->on('classes');
            $table->foreign('mentor_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('class_mentors');
    }
}
