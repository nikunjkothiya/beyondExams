<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserRatingsToCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->bigInteger('rating_sum')->after('parent_id')->default(0);
            $table->bigInteger('rated_user')->after('rating_sum')->default(0);
            $table->bigInteger('video_count')->after('rated_user')->default(0);
            $table->bigInteger('total_time')->after('video_count')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('categories', 'rating_sum')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('rating_sum');
            });
        }
        if (Schema::hasColumn('categories', 'rated_user')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('rated_user');
            });
        }
        if (Schema::hasColumn('categories', 'video_count')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('video_count');
            });
        }
        if (Schema::hasColumn('categories', 'total_time')) {
            Schema::table('categories', function (Blueprint $table) {
                $table->dropColumn('total_time');
            });
        }
    }
}
