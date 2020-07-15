<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyProductsAndTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products',function($table){
            $table->dropColumn('price');
            $table->dropColumn('months');
        });

        Schema::table('transactions',function($table){
            $table->dropColumn('datetime');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products',function($table){
            $table->unsignedDecimal('price', 8, 2);
            $table->unsignedBigInteger('months');
        });
        Schema::table('transactions',function($table){
            $table->datetime('datetime');
        });
    }
}
