<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EligibleRegionOpportunity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('eligible_region_opportunity', function (Blueprint $table) {
            $table->unsignedBigInteger('opportunity_id');
            $table->unsignedBigInteger('eligible_region_id');
        });
        Schema::table('eligible_region_opportunity',function($table){
            $table->foreign('opportunity_id')->references('id')->on('opportunities');
            $table->foreign('eligible_region_id')->references('id')->on('eligible_regions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('eligible_region_opportunity');
    }
}
