<?php

use Illuminate\Database\Seeder;

class realOrganisationsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('organisations')->delete();
        
        \DB::table('organisations')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Super User',
                'unique_id' => '123456789',
                'email' => 'admin@precisely.co.in',
                'role_id' => 3,
                'password' => '$2y$10$QDb56//0AYjdJpLaqa.Aiew5Q/JYSnmJhDvlS1qDQ8XbF4dgdnpP.',
                'avatar' => 'https://i.ibb.co/KbPQH7j/root.jpg',
                'remember_token' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
        ));
        
        
    }
}