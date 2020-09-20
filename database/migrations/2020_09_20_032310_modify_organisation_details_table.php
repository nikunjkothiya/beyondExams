<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyOrganisationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('organisation_details', function (Blueprint $table) {
            $table->dropForeign('organisation_details_organisation_id_foreign');
            $table->dropColumn('organisation_id');
            $table->dropColumn('org_name');
            $table->dropColumn('email');
            $table->dropColumn('phone');
            $table->dropColumn('profile_link');
            $table->dropColumn('slug');
            $table->unsignedBigInteger('user_id');
        });

        Schema::table('organisation_details',function($table){
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('organisation_details', function (Blueprint $table) {
            $table->unsignedBigInteger('organisation_id');
            $table->string('org_name')->default(3);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('profile_link')->nullable();
            $table->string('slug')->nullable();
            $table->dropForeign('organisation_details_user_id_foreign');
            $table->dropColumn('user_id');
        });

        Schema::table('organisation_details',function($table){
            $table->foreign('organisation_id')->references('id')->on('organisations');
        });
    }
}
