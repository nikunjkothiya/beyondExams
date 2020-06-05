<?php

use Illuminate\Database\Seeder;

class TagTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = array(
			array('type' => 'Tags'),
			array('type' => 'Qualifications'),
			array('type' => 'Disciplines'),
		);
		DB::table('tag_types')->insert($types);
    }
}
