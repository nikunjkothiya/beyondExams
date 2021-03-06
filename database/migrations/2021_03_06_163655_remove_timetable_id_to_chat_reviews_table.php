<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveTimetableIdToChatReviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('chat_reviews', function (Blueprint $table) {
            $table->dropForeign(['timetable_id']);
            $table->dropColumn('timetable_id');
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('chat_reviews', function (Blueprint $table) {
        
        });
    }
}
