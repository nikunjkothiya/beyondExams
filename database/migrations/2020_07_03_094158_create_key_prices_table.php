<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeyPricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('key_prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('key_id');
            $table->unsignedBigInteger('currency_id');
            $table->integer('price');
        });
        Schema::table('key_prices',function($table){
            $table->foreign('key_id')->references('id')->on('keys');
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
        Schema::dropIfExists('key_prices');
    }
}
