<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users',function($table){
            $table->string('firstname')->nullable();
            $table->string('lastname')->nullable();
            $table->integer('num_followers')->default(0);
            $table->integer('num_following')->default(0);
            $table->unsignedBigInteger('language_id')->default(3);
            $table->string('profile_link')->nullable();
            $table->string('slug')->nullable()->unique();

        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('language_id')->references('id')->on('languages');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users',function($table){
            $table->dropColumn('firstname');
            $table->dropColumn('lastname');
            $table->dropColumn('num_followers');
            $table->dropColumn('num_following');
            $table->dropForeign('users_language_id_foreign');
            $table->dropColumn('language_id');
            $table->dropColumn('profile_link');
            $table->dropColumn('slug');

        });
    }
}
