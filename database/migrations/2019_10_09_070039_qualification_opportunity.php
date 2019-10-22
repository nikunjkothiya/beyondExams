<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class QualificationOpportunity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opportunity_qualification', function (Blueprint $table) {
            $table->unsignedBigInteger('qualification_id');
            $table->unsignedBigInteger('opportunity_id');
        });
        Schema::table('opportunity_qualification',function($table){
            $table->foreign('qualification_id')->references('id')->on('qualifications');
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
        Schema::dropIfExists('opportunity_qualification');
    }
}
