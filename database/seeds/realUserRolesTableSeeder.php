<?php

use Illuminate\Database\Seeder;

class realUserRolesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_roles')->delete();
        
        \DB::table('user_roles')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 6,
                'is_user' => 1,
                'is_mentor' => 0,
                'created_at' => '2020-06-18 10:32:53',
                'updated_at' => '2020-06-18 10:32:53',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 9,
                'is_user' => 1,
                'is_mentor' => 0,
                'created_at' => '2020-06-18 10:54:06',
                'updated_at' => '2020-06-18 10:54:06',
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 22,
                'is_user' => 1,
                'is_mentor' => 0,
                'created_at' => '2020-06-18 13:19:57',
                'updated_at' => '2020-06-18 13:19:57',
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => 29,
                'is_user' => 1,
                'is_mentor' => 0,
                'created_at' => '2020-06-18 15:55:22',
                'updated_at' => '2020-06-18 15:55:22',
            ),
            4 => 
            array (
                'id' => 5,
                'user_id' => 30,
                'is_user' => 1,
                'is_mentor' => 0,
                'created_at' => '2020-06-18 18:58:37',
                'updated_at' => '2020-06-18 18:58:37',
            ),
            5 => 
            array (
                'id' => 6,
                'user_id' => 8,
                'is_user' => 1,
                'is_mentor' => 0,
                'created_at' => '2020-06-18 22:00:04',
                'updated_at' => '2020-06-18 22:00:04',
            ),
            6 => 
            array (
                'id' => 7,
                'user_id' => 7,
                'is_user' => 1,
                'is_mentor' => 0,
                'created_at' => '2020-06-20 13:15:21',
                'updated_at' => '2020-06-20 13:15:21',
            ),
        ));
        
        
    }
}