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
			array('tag' => 'English', 'tag_type_id' => 1),
			array('tag' => 'Mathematics', 'tag_type_id' => 1),
			array('tag' => 'Science', 'tag_type_id' => 1),
			array('tag' => 'Hindi', 'tag_type_id' => 1),
            array('tag' => 'Social Science', 'tag_type_id' => 1),
            array('tag' => 'Computer Science', 'tag_type_id' => 1),
            array('tag' => 'Sanskrit', 'tag_type_id' => 1),
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
