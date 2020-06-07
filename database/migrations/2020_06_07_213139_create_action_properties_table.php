<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActionPropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_properties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('act_id');
            $table->string('key');
            $table->integer('value');
            $table->timestamps();
        });

        Schema::table('action_properties',function($table) {
            $table->foreign('act_id')->references('id')->on('action_users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_properties');
    }
}
