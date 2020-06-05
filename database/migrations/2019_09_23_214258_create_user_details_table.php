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
            $table->unsignedBigInteger('language_id')->default(3);
            $table->string('email')->nullable();
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->string('college')->nullable();
            $table->string('city')->nullable();
            $table->unsignedDecimal('gpa', 4, 2)->nullable();
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('discipline_id')->nullable();
            $table->unsignedBigInteger('qualification_id')->nullable();
            $table->timestamps();
        });
        Schema::table('user_details',function($table){
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('discipline_id')->references('id')->on('disciplines');
            $table->foreign('qualification_id')->references('id')->on('qualifications');
            $table->foreign('language_id')->references('id')->on('languages');
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
