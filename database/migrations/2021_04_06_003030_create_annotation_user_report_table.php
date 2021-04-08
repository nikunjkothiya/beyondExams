<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAnnotationUserReportTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('annotation_user_report', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('video_annotation_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });

        Schema::table('annotation_user_report',function($table){
            $table->foreign('video_annotation_id')->references('id')->on('video_annotations')->onDelete('cascade');
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
        Schema::dropIfExists('annotation_user_report');
    }
}
