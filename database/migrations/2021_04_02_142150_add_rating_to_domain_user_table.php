<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRatingToDomainUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('domain_user', function (Blueprint $table) {
            $table->enum('experience', ['1', '2', '3', '4', '5'])->default(1)->after('domain_id')->comment('Rating: 1 to 5');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('domain_user', 'experience')) {
            Schema::table('domain_user', function (Blueprint $table) {
                $table->dropColumn('experience');
            });
        }
    }
}
