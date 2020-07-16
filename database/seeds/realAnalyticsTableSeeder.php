<?php

use Illuminate\Database\Seeder;

class realAnalyticsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('analytics')->delete();
        
        
    }
}