<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganisationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organisation_details', function (Blueprint $table) {
                    $table->bigIncrements('id');
                    $table->unsignedBigInteger('organisation_id');
                    $table->unsignedBigInteger('org_name')->default(3);
                    $table->string('contact_person')->nullable();
                    $table->string('branch')->nullable();
                    $table->unsignedBigInteger('country_id')->nullable();
                    $table->string('email')->nullable();
                    $table->string('phone')->nullable();
                    $table->timestamps();
                });
                Schema::table('organisation_details',function($table){
                    $table->foreign('organisation_id')->references('id')->on('organisations');
                    $table->foreign('country_id')->references('id')->on('countries');
                });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organisation_details');
    }
}
