<?php

use Illuminate\Database\Seeder;

class SessionTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $session_types = array(
			array('title' => 'Broadcast'),
			array('title' => 'Doubt Session'),
        );
		DB::table('session_types')->insert($session_types);
    }
}
