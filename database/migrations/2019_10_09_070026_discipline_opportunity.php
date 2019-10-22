<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DisciplineOpportunity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discipline_opportunity', function (Blueprint $table) {
            $table->unsignedBigInteger('discipline_id');
            $table->unsignedBigInteger('opportunity_id');
        });
        Schema::table('discipline_opportunity',function($table){
            $table->foreign('discipline_id')->references('id')->on('disciplines');
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
        Schema::dropIfExists('discipline_opportunity');
    }
}
