<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        private $file_parameters = ["url", "thumbnail", "type", "length", "title", "author", "designation", "profile_pic"];
        Schema::create('resources', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('url')->nullable();
            $table->string('thumbnail_url')->nullable();
            $table->integer('duration')->nullable();
            $table->string('title');
            $table->mediumText('description');
            $table->string('slug');
            $table->integer('num_likes')->default(0);
            $table->integer('num_comments')->default(0);
            $table->integer('num_subscribers')->default(0);
            $table->unsignedBigInteger('author_id');
            $table->timestamps();
        });

        Schema::table('resources',function($table){
            $table->foreign('author_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('resources');
    }
}
