<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('chat_id');
            $table->string('message');
            $table->unsignedBigInteger('type_id')->default(1);
            $table->unsignedBigInteger('sender_id');
            $table->timestamps();
            $table->integer('is_child')->default(-1);
        });
        Schema::table('chat_messages',function($table){
            $table->foreign('chat_id')->references('id')->on('chats');
            $table->foreign('type_id')->references('id')->on('message_types');
            $table->foreign('sender_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');
    }
}
