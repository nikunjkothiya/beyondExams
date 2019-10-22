<?php

use Illuminate\Database\Seeder;

class DisciplineTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $disciplines = array(
			array('discipline' => 'Engineering'),
			array('discipline' => 'Medicine'),
			array('discipline' => 'Management'),
			array('discipline' => 'Humanities'),
			array('discipline' => 'Science'),
		);
		DB::table('disciplines')->insert($disciplines);
    }
}
