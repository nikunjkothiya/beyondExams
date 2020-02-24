<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOpportunityComments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opportunity_comments', function (Blueprint $table) {
            $table->unsignedBigInteger('opportunity_id');
            $table->unsignedBigInteger('comment_id');
            $table->timestamps();
        });
        Schema::table('opportunity_comments',function($table){
            $table->foreign('opportunity_id')->references('id')->on('opportunities');
            $table->foreign('comment_id')->references('id')->on('list_comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opportunity_comments');
    }
}
