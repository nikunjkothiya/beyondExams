<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLiveTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_live', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('peer_id')->unique();
            $table->unsignedBigInteger('user_id');
            $table->boolean('live')->default(1);
            $table->timestamps();
        });
        Schema::table('user_live',function($table){
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_live');
    }
}
