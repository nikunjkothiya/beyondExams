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
            $table->BigInteger('price_inr')->default();
            $table->BigInteger('price_usd')->default();
            $table->timestamps();
        });
        Schema::table('key_prices',function($table){
            $table->foreign('key_id')->references('id')->on('keys');
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
