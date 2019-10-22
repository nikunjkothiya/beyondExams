<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QualificationUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qualification_user', function (Blueprint $table) {
            $table->unsignedBigInteger('qualification_id');
            $table->unsignedBigInteger('user_id');
        });
        Schema::table('qualification_user',function($table){
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('qualification_id')->references('id')->on('qualifications');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qualification_user');
    }
}
