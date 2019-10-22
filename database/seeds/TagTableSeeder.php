<?php

use Illuminate\Database\Seeder;

class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = array(
			array('tag' => 'Conferences'),
			array('tag' => 'Competitions'),
			array('tag' => 'Scholarships'),
			array('tag' => 'Awards'),
            array('tag' => 'Internships'),
            array('tag' => 'Admissions'),
            array('tag' => 'Summer/Winter Schools'),
            array('tag' => 'Fellowships'),
            array('tag' => 'Grants'),
            array('tag' => 'Workshops'),
		);
		DB::table('tags')->insert($tags);
    }
}
