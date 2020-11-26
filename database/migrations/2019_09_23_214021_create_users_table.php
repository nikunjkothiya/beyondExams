<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name')->nullable();
            $table->string('unique_id')->unique();
            $table->string('avatar')->nullable();
            $table->string('email')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->string('phone')->nullable();
            $table->string('slug')->nullable()->unique();
            $table->string('profile_link')->nullable();
            $table->string('flag')->default(0);
            $table->unsignedBigInteger('role_id')->nullable();
            $table->unsignedBigInteger('language_id')->default(3);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->string('password')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
        Schema::table('users',function($table){
            $table->foreign('role_id')->references('id')->on('roles');
            $table->foreign('language_id')->references('id')->on('languages');
            $table->foreign('country_id')->references('id')->on('countries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
