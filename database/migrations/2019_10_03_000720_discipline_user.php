<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DisciplineUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discipline_user', function (Blueprint $table) {
            $table->unsignedBigInteger('discipline_id');
            $table->unsignedBigInteger('user_id');
        });
        Schema::table('discipline_user',function($table){
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('discipline_id')->references('id')->on('disciplines');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discipline_user');
    }
}
