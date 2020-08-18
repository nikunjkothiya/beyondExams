<?php

use Illuminate\Database\Seeder;

class realUserCommentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_comments')->delete();
        
        \DB::table('user_comments')->insert(array (
            0 => 
            array (
                'user_id' => 6,
                'comment_id' => 1,
                'created_at' => '2020-06-08 12:32:17',
                'updated_at' => '2020-06-08 12:32:17',
            ),
            1 => 
            array (
                'user_id' => 6,
                'comment_id' => 2,
                'created_at' => '2020-06-08 12:33:02',
                'updated_at' => '2020-06-08 12:33:02',
            ),
            2 => 
            array (
                'user_id' => 6,
                'comment_id' => 3,
                'created_at' => '2020-06-08 12:45:59',
                'updated_at' => '2020-06-08 12:45:59',
            ),
            3 => 
            array (
                'user_id' => 6,
                'comment_id' => 4,
                'created_at' => '2020-06-08 12:46:10',
                'updated_at' => '2020-06-08 12:46:10',
            ),
            4 => 
            array (
                'user_id' => 6,
                'comment_id' => 5,
                'created_at' => '2020-06-08 15:07:20',
                'updated_at' => '2020-06-08 15:07:20',
            ),
            5 => 
            array (
                'user_id' => 6,
                'comment_id' => 6,
                'created_at' => '2020-06-08 15:10:04',
                'updated_at' => '2020-06-08 15:10:04',
            ),
            6 => 
            array (
                'user_id' => 6,
                'comment_id' => 7,
                'created_at' => '2020-06-11 18:21:09',
                'updated_at' => '2020-06-11 18:21:09',
            ),
            7 => 
            array (
                'user_id' => 8,
                'comment_id' => 8,
                'created_at' => '2020-06-14 10:29:28',
                'updated_at' => '2020-06-14 10:29:28',
            ),
            8 => 
            array (
                'user_id' => 8,
                'comment_id' => 9,
                'created_at' => '2020-06-14 10:29:37',
                'updated_at' => '2020-06-14 10:29:37',
            ),
            9 => 
            array (
                'user_id' => 8,
                'comment_id' => 10,
                'created_at' => '2020-06-14 10:35:09',
                'updated_at' => '2020-06-14 10:35:09',
            ),
        ));
        
        
    }
}