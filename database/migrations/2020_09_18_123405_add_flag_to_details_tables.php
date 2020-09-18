<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFlagToDetailsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('student_details', function (Blueprint $table) {
            $table->string('flag')->default(1);
        });

        Schema::table('mentor_details', function (Blueprint $table) {
            $table->string('flag')->default(0);
        });

        Schema::table('organisation_details', function (Blueprint $table) {
            $table->string('flag')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('student_details', function (Blueprint $table) {
            $table->dropColumn('flag');
        });

        Schema::table('mentor_details', function (Blueprint $table) {
            $table->dropColumn('flag');
        });

        Schema::table('organisation_details', function (Blueprint $table) {
            $table->dropColumn('flag');
        });
    }
}
