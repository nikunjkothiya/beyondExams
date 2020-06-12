<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLegacyOpportunityTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legacy_opportunity', function (Blueprint $table) {
            $table->unsignedBigInteger("phoenix_opportunity_id")->unique();
            $table->bigInteger("legacy_opportunity_id")->unique();
            $table->timestamps();
        });

        Schema::table('legacy_opportunity',function($table){
            $table->foreign('phoenix_opportunity_id')->references('id')->on('opportunity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('legacy_opportunity');
    }
}
