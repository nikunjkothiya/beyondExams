<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceTimelinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_timelines', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('resource_id');
            $table->unsignedBigInteger('user_id');
            $table->integer('priority');
            $table->timestamps();
        });
        Schema::table('resource_timelines',function($table){
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
        Schema::dropIfExists('resource_timelines');
    }
}
