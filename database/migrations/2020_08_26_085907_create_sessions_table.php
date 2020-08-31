<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class CreateSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sessions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title');
            $table->unsignedBigInteger('host_id');
            $table->string('peer_id')->nullable();
            $table->string('user_limit')->default(500);
            $table->boolean('live')->default(1);
            $table->dateTime('live_time')->default(Carbon::now());
            $table->boolean('restricted')->default(0);
            $table->unsignedBigInteger('session_type')->default(1);
            $table->timestamps();
        });
        Schema::table('sessions',function($table) {
            $table->foreign('host_id')->references('id')->on('users');
            $table->foreign('session_type')->references('id')->on('session_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sessions');
    }
}
