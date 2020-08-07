<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserViewedOpportunities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_viewed_opportunities', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('opportunity_id');
        });
        Schema::table('user_viewed_opportunities',function($table){
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('table_user_viewed_opportunities');
    }
}
