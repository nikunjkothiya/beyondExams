<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_groups', function (Blueprint $table) {
            $table->bigIncrements('id');
             $table->unsignedBigInteger('opportunity_id');
            $table->unsignedBigInteger('chat_id');
            $table->timestamps();
        });
        Schema::table('chat_groups',function($table){
            $table->foreign('chat_id')->references('id')->on('chats');
            $table->foreign('opportunity_id')->references('id')->on('opportunities');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_groups');
    }
}
