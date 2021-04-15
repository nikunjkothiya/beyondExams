<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateVotesToVideoNoteVotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_note_votes', function (Blueprint $table) {
            $table->dropColumn('upvote');
            $table->dropColumn('downvote');
            $table->tinyInteger('vote')->after('note_id')->default(0)->comment('-1 = Downvote, 0 = no vote, 1 = Upvote');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_note_votes', function (Blueprint $table) {
            $table->tinyInteger('upvote')->default(0)->comment('0 = no vote, 1 = vote');
            $table->tinyInteger('downvote')->default(0)->comment('0 = no vote, 1 = vote');
        });
        if (Schema::hasColumn('video_note_votes', 'vote')) {
            Schema::table('video_note_votes', function (Blueprint $table) {
                $table->dropColumn('vote');
            });
        }
    }
}
