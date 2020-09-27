<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourceCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('resource_comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('resource_id');
            $table->unsignedBigInteger('user_id');
            $table->string('message');
            $table->unsignedBigInteger('message_type')->default(1);
            $table->unsignedBigInteger('is_child')->nullable();
            $table->timestamps();
        });

        Schema::table('resource_comments',function($table){
            $table->foreign('resource_id')->references('id')->on('resources');
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('message_type')->references('id')->on('message_types');
            $table->foreign('is_child')->references('id')->on('resource_comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resource_comments');
    }
}
