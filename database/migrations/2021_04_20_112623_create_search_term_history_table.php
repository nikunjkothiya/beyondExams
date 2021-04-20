<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSearchTermHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('search_term_history', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('search_id');
            $table->bigInteger('count')->default(1);
            $table->timestamps();
        });

        Schema::table('search_term_history',function($table){
            $table->foreign('search_id')->references('id')->on('searches')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('search_term_history');
    }
}
