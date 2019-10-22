<?php

use Illuminate\Database\Seeder;

class QualificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $qualifications = array(
			array('qualification' => 'Post Doctorate'),
			array('qualification' => 'Doctorate'),
			array('qualification' => 'Masters'),
			array('qualification' => 'Bachelors'),
			array('qualification' => 'School'),
		);
		DB::table('qualifications')->insert($qualifications);
    }
}
