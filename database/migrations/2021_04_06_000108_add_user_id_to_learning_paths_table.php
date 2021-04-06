<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToLearningPathsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('learning_paths', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id')->default(1);
        });

        Schema::table('learning_paths',function($table){
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
            if (Schema::hasColumn('learning_paths', 'user_id')) {
                Schema::table('learning_paths', function (Blueprint $table) {
                    $table->dropForeign('learning_paths_user_id_foreign');
                    $table->dropColumn('user_id');
                });
            }
    }
}
