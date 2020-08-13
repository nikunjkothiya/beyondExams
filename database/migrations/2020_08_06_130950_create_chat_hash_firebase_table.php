<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatHashFirebaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_hash_firebase', function (Blueprint $table) {
            $table->bigIncrements('id')->unique();
            $table->unsignedBigInteger('chat_id');
            $table->unsignedBigInteger('hash_firebase_id');
            $table->timestamps();
        });

        Schema::table('chat_hash_firebase',function($table){
                    $table->foreign('chat_id')->references('id')->on('chats');
                    $table->foreign('hash_firebase_id')->references('id')->on('hash_firebase');
                });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_hash_firebase');
    }
}
