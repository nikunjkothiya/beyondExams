<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDateToUserCerticates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_certicates', function (Blueprint $table) {
            $table->string('issuing_date')->after('organization')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('user_certicates', 'issuing_date')) {
            Schema::table('user_certicates', function (Blueprint $table) {
                $table->dropColumn('issuing_date');
            });
        }
    }
}
