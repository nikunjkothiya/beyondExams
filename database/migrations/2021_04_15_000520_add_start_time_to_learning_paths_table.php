<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStartTimeToLearningPathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->string('start_time')->after('ordering')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('learning_paths', 'start_time')) {
            Schema::table('learning_paths', function (Blueprint $table) {
                $table->dropColumn('start_time');
            });
        }
    }
}
