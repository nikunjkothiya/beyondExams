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
        
        \DB::table('analytics')->insert(array (
            0 => 
            array (
                'id' => 1,
                'key' => 'score',
                'value' => -1,
                'action_id' => 6,
                'user_id' => 1,
                'opportunity_id' => 1,
                'created_at' => '2020-06-09 17:53:04',
                'updated_at' => '2020-06-09 17:53:04',
            ),
            1 => 
            array (
                'id' => 2,
                'key' => NULL,
                'value' => NULL,
                'action_id' => 1,
                'user_id' => 2,
                'opportunity_id' => NULL,
                'created_at' => '2020-06-19 18:55:51',
                'updated_at' => '2020-06-19 18:55:51',
            ),
            2 => 
            array (
                'id' => 3,
                'key' => NULL,
                'value' => NULL,
                'action_id' => 1,
                'user_id' => 2,
                'opportunity_id' => NULL,
                'created_at' => '2020-06-19 19:52:31',
                'updated_at' => '2020-06-19 19:52:31',
            ),
            3 => 
            array (
                'id' => 4,
                'key' => NULL,
                'value' => NULL,
                'action_id' => 1,
                'user_id' => 2,
                'opportunity_id' => NULL,
                'created_at' => '2020-06-19 21:02:29',
                'updated_at' => '2020-06-19 21:02:29',
            ),
        ));
        
        
    }
}