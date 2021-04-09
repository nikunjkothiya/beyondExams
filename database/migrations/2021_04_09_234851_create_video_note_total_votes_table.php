<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVideoNoteTotalVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('video_note_total_votes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('video_annotation_id');
            $table->bigInteger('total_upvote')->default(0);
            $table->bigInteger('total_downvote')->default(0);
            $table->timestamps();
        });

        Schema::table('video_note_total_votes',function($table){
            $table->foreign('video_annotation_id')->references('id')->on('video_annotations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_note_total_votes');
    }
}
