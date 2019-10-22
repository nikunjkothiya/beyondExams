<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('language_id');
            $table->string('firstname');
            $table->string('lastname');
            $table->string('college');
            $table->string('city');
            $table->unsignedDecimal('gpa', 4, 2);
            $table->unsignedBigInteger('qualification_id');
            $table->unsignedBigInteger('discipline_id');
            $table->unsignedBigInteger('country_id');
            $table->timestamps();
        });
        Schema::table('user_details',function($table){
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('language_id')->references('id')->on('languages');
            $table->foreign('qualification_id')->references('id')->on('qualifications');
            $table->foreign('discipline_id')->references('id')->on('disciplines');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_details');
    }
}
