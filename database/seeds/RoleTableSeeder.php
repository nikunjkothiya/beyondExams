<?php

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roles = array(
			array('role' => 'User'),
			array('role' => 'Mentor'),
			array('role' => 'Admin'),
			array('role' => 'Institute'),
		);
		DB::table('roles')->insert($roles);
    }
}
