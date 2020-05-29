<?php

use Illuminate\Database\Seeder;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = array(
			array('name' => 'Precisely','unique_id' => '123456789','email' => 'admin@precisely.co.in','role_id' => 3,'password' => bcrypt('Desh@1996'),'avatar'=>'https://i.ibb.co/KbPQH7j/root.jpg'),
		);
		DB::table('users')->insert($users);
    }
}
