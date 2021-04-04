<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStateIdToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('state_id')->after('country_id')->nullable();
            $table->date('dob')->after('state_id')->nullable()->comment('date of birth');
        });

        Schema::table('users',function($table){
            $table->foreign('state_id')->references('id')->on('states')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('users', 'dob')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('dob');
            });
        }
        if (Schema::hasColumn('users', 'state_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropForeign('users_state_id_foreign');
                $table->dropColumn('state_id');
            });
        }
    }
}
