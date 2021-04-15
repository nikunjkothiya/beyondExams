<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReadingMaterialToNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('notes', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->default(1)->after('title');
            $table->string('size')->after('resource_url');
            $table->integer('total_pages')->after('size');
        });

        Schema::table('notes',function($table){
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
        if (Schema::hasColumn('notes', 'user_id')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->dropForeign('notes_user_id_foreign');
                $table->dropColumn('user_id');
            });
        }
        if (Schema::hasColumn('notes', 'size')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->dropColumn('size');
            });
        }
        if (Schema::hasColumn('notes', 'total_pages')) {
            Schema::table('notes', function (Blueprint $table) {
                $table->dropColumn('total_pages');
            });
        }
    }
}
