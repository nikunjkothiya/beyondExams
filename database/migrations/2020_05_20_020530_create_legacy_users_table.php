<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegacyUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legacy_users', function (Blueprint $table) {
            $table->unsignedBigInteger("phoenix_user_id")->unique();
            $table->bigInteger("legacy_user_id")->unique();
            $table->timestamps();
        });

        Schema::table('legacy_users',function($table){
            $table->foreign('phoenix_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('legacy_users');
    }
}
