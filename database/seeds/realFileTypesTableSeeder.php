<?php

use Illuminate\Database\Seeder;

class realFileTypesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('file_types')->delete();
        
        \DB::table('file_types')->insert(array (
            0 => 
            array (
                'id' => 1,
                'type' => 'blogs',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'type' => 'articles',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'type' => 'videos',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'type' => 'playlist',
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}