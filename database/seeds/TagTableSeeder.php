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
			array('tag' => 'Conferences', 'tag_type_id' => 1),
			array('tag' => 'Competitions', 'tag_type_id' => 1),
			array('tag' => 'Scholarships', 'tag_type_id' => 1),
			array('tag' => 'Awards', 'tag_type_id' => 1),
            array('tag' => 'Internships', 'tag_type_id' => 1),
            array('tag' => 'Admissions', 'tag_type_id' => 1),
            array('tag' => 'Summer/Winter Schools', 'tag_type_id' => 1),
            array('tag' => 'Fellowships', 'tag_type_id' => 1),
            array('tag' => 'Grants', 'tag_type_id' => 1),
            array('tag' => 'Workshops', 'tag_type_id' => 1),
            array('tag' => 'Post Doctorate', 'tag_type_id' => 2),
            array('tag' => 'Doctorate', 'tag_type_id' => 2),
            array('tag' => 'Masters', 'tag_type_id' => 2),
            array('tag' => 'Bachelors', 'tag_type_id' => 2),
            array('tag' => 'School', 'tag_type_id' => 2),
            array('tag' => 'Engineering', 'tag_type_id' => 3),
            array('tag' => 'Medicine', 'tag_type_id' => 3),
            array('tag' => 'Management', 'tag_type_id' => 3),
            array('tag' => 'Humanities', 'tag_type_id' => 3),
            array('tag' => 'Science', 'tag_type_id' => 3),
		);
		DB::table('tags')->insert($tags);
    }
}
