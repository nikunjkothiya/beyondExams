<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCommonDataFromDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mentor_details', function (Blueprint $table) {
            $table->dropColumn('firstname');
            $table->dropColumn('lastname');
            $table->dropColumn('email');
            $table->dropColumn('profile_link');
            $table->dropColumn('slug');
        });

        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn('college');
            $table->dropColumn('city');
            $table->dropColumn('gpa');
            $table->dropForeign('user_details_country_id_foreign');
            $table->dropColumn('country_id');
            $table->dropForeign('user_details_discipline_id_foreign');
            $table->dropColumn('discipline_id');
            $table->dropForeign('user_details_qualification_id_foreign');
            $table->dropColumn('qualification_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
