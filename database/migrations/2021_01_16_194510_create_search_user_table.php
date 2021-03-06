<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('search_id');
            $table->unsignedBigInteger('user_id');
            $table->string('type');
            $table->timestamps();

            $table->foreign('search_id')->references('id')->on('searches')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_users');
    }
}
