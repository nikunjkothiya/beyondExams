<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserIdToCategoryKeywordTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('category_keyword', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('id')->default(1);
        });

        Schema::table('category_keyword',function($table){
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
        if (Schema::hasColumn('category_keyword', 'user_id')) {
            Schema::table('category_keyword', function (Blueprint $table) {
                $table->dropForeign('category_keyword_user_id_foreign');
                $table->dropColumn('user_id');
            });
        }
    }
}
