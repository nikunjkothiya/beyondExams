<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->string('profile_link')->nullable();
            $table->string('slug')->nullable();
        });

        Schema::table('mentor_details', function (Blueprint $table) {
            $table->string('slug')->nullable();
        });

        Schema::table('organisation_details', function (Blueprint $table) {
            $table->string('profile_link')->nullable();
            $table->string('slug')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_details', function (Blueprint $table) {
            $table->dropColumn('profile_link');
            $table->dropColumn('slug');
        });

        Schema::table('mentor_details', function (Blueprint $table) {
            $table->dropColumn('slug');
        });

        Schema::table('organisation_details', function (Blueprint $table) {
            $table->dropColumn('profile_link');
            $table->dropColumn('slug');
        });
    }
}
