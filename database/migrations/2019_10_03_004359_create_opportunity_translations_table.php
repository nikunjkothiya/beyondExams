<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOpportunityTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('opportunity_translations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('opportunity_id');
            $table->string('locale')->index();
            $table->string('title');
            $table->text('description');

            $table->unique(['opportunity_id', 'locale']);
            $table->foreign('opportunity_id')->references('id')->on('opportunities')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('opportunity_translations');
    }
}
