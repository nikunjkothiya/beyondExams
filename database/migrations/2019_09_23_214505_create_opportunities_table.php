<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpportunitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opportunities', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('image');
            $table->string('link');
            $table->date('deadline');
            $table->string('slug');
            $table->unsignedBigInteger('fund_type_id');
            $table->unsignedBigInteger('opportunity_location_id');
            $table->timestamps();
        });
        Schema::table('opportunities',function($table){
            $table->foreign('fund_type_id')->references('id')->on('fund_types');
            $table->foreign('opportunity_location_id')->references('id')->on('opportunity_locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opportunities');
    }
}
