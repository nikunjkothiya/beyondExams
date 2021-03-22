<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsPublicToVideoAnnotationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_annotations', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id');
            $table->tinyInteger('is_public')->after('annotation')->default(1)->comment('0=private,1=public');
        });

        Schema::table('video_annotations',function($table){
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_annotations', function (Blueprint $table) {
            //
        });
    }
}
