<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTitleToVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->string('title')->after('url')->nullable();
            $table->longText('description')->after('title')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('videos', 'title')) {
            Schema::table('videos', function (Blueprint $table) {
                $table->dropColumn('title');
            });
        }
        if (Schema::hasColumn('videos', 'description')) {
            Schema::table('videos', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
}
