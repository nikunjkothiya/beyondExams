<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_likes', function (Blueprint $table) {
            $table->unsignedBigInteger('resource_id');
            $table->unsignedBigInteger('user_id');
        });

        Schema::table('resource_likes',function($table){
            $table->foreign('resource_id')->references('id')->on('resources');
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
        Schema::dropIfExists('resource_likes');
    }
}
