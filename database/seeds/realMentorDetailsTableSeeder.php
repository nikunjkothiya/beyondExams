<?php

use Illuminate\Database\Seeder;

class realMentorDetailsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('mentor_details')->delete();
        
        \DB::table('mentor_details')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 6,
                'firstname' => 'Raghav',
                'lastname' => 'Dhingra',
                'email' => 'raghav.dhingra15@gmail.com',
                'designation' => 'CEO',
                'organisation' => NULL,
                'profile_link' => 'https://raghavdhingra.com',
                'created_at' => '2020-06-20 11:38:35',
                'updated_at' => '2020-06-20 11:38:35',
            ),
        ));
        
        
    }
}