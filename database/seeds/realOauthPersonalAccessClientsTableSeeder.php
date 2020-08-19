<?php

use Illuminate\Database\Seeder;

class realOauthPersonalAccessClientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_personal_access_clients')->delete();
        
        \DB::table('oauth_personal_access_clients')->insert(array (
            0 => 
            array (
                'id' => 1,
                'client_id' => 1,
                'created_at' => '2020-06-02 21:08:02',
                'updated_at' => '2020-06-02 21:08:02',
            ),
            1 => 
            array (
                'id' => 2,
                'client_id' => 3,
                'created_at' => '2020-06-06 15:12:53',
                'updated_at' => '2020-06-06 15:12:53',
            ),
            2 => 
            array (
                'id' => 3,
                'client_id' => 5,
                'created_at' => '2020-06-07 22:11:34',
                'updated_at' => '2020-06-07 22:11:34',
            ),
            3 => 
            array (
                'id' => 4,
                'client_id' => 7,
                'created_at' => '2020-06-07 23:11:58',
                'updated_at' => '2020-06-07 23:11:58',
            ),
            4 => 
            array (
                'id' => 5,
                'client_id' => 9,
                'created_at' => '2020-06-08 09:26:45',
                'updated_at' => '2020-06-08 09:26:45',
            ),
            5 => 
            array (
                'id' => 6,
                'client_id' => 11,
                'created_at' => '2020-06-13 10:11:36',
                'updated_at' => '2020-06-13 10:11:36',
            ),
            6 => 
            array (
                'id' => 7,
                'client_id' => 13,
                'created_at' => '2020-06-18 10:39:23',
                'updated_at' => '2020-06-18 10:39:23',
            ),
            7 => 
            array (
                'id' => 8,
                'client_id' => 15,
                'created_at' => '2020-06-21 00:31:30',
                'updated_at' => '2020-06-21 00:31:30',
            ),
        ));
        
        
    }
}