<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePremiumPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('premium_plans', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('plan_name');
            $table->integer('price');
            $table->integer('months');
            $table->integer('enabled')->default(1);
            $table->unsignedBigInteger('currency_id');
            $table->timestamps();
        });
        Schema::table('premium_plans',function($table){
            $table->foreign('currency_id')->references('id')->on('currencies');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('premium_plans');
    }
}
