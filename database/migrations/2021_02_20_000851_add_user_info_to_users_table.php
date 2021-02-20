<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUserInfoToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('short_bio')->nullable()->after('slug');
            $table->string('facebook_link')->nullable()->after('short_bio');
            $table->string('instagram_link')->nullable()->after('facebook_link');
            $table->string('github_link')->nullable()->after('instagram_link');
            $table->string('twitter_url')->nullable()->after('github_link');
            $table->string('linkedin_url')->nullable()->after('twitter_url');   
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
}
