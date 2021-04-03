<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEducationUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('education_user', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('institutes_id');
            $table->unsignedBigInteger('education_standard_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::table('education_user',function($table){
            $table->foreign('education_standard_id')->references('id')->on('education_standards')->onDelete('cascade');
            $table->foreign('institutes_id')->references('id')->on('institutes')->onDelete('cascade');
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
        Schema::dropIfExists('education_user');
    }
}
