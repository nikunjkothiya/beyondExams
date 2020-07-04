<?php

use Illuminate\Database\Seeder;

class realListCommentsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('list_comments')->delete();
        
        \DB::table('list_comments')->insert(array (
            0 => 
            array (
                'id' => 1,
                'message' => 'Really awesome',
                'created_at' => '2020-06-08 12:32:17',
                'updated_at' => '2020-06-08 12:32:17',
            ),
            1 => 
            array (
                'id' => 2,
                'message' => 'Really awesome',
                'created_at' => '2020-06-08 12:33:02',
                'updated_at' => '2020-06-08 12:33:02',
            ),
            2 => 
            array (
                'id' => 3,
                'message' => 'Really awesome',
                'created_at' => '2020-06-08 12:45:59',
                'updated_at' => '2020-06-08 12:45:59',
            ),
            3 => 
            array (
                'id' => 4,
                'message' => 'That\'s a great opportunity',
                'created_at' => '2020-06-08 12:46:10',
                'updated_at' => '2020-06-08 12:46:10',
            ),
            4 => 
            array (
                'id' => 5,
                'message' => 'fds',
                'created_at' => '2020-06-08 15:07:20',
                'updated_at' => '2020-06-08 15:07:20',
            ),
            5 => 
            array (
                'id' => 6,
                'message' => 'hey',
                'created_at' => '2020-06-08 15:10:04',
                'updated_at' => '2020-06-08 15:10:04',
            ),
            6 => 
            array (
                'id' => 7,
                'message' => 'hey that\'s a very nice opportunity',
                'created_at' => '2020-06-11 18:21:09',
                'updated_at' => '2020-06-11 18:21:09',
            ),
            7 => 
            array (
                'id' => 8,
                'message' => 'GG',
                'created_at' => '2020-06-14 10:29:28',
                'updated_at' => '2020-06-14 10:29:28',
            ),
            8 => 
            array (
                'id' => 9,
                'message' => 'GG\\',
                'created_at' => '2020-06-14 10:29:37',
                'updated_at' => '2020-06-14 10:29:37',
            ),
            9 => 
            array (
                'id' => 10,
                'message' => 'vsa',
                'created_at' => '2020-06-14 10:35:09',
                'updated_at' => '2020-06-14 10:35:09',
            ),
        ));
        
        
    }
}