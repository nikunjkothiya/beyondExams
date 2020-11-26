<?php

use Illuminate\Database\Seeder;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $currencies = array(
            array('title' => 'English', 'level' => 1),
            array('title' => 'Mathematics', 'level' => 1),
            array('title' => 'Science', 'level' => 1),
            array('title' => 'Computer Science', 'level' => 1),
            array('title' => 'Nouns', 'level' => 2, 'parent_id' => 1),
            array('title' => 'Pronouns', 'level' => 2, 'parent_id' => 1),
            array('title' => 'Adjectives', 'level' => 2, 'parent_id' => 1),
            array('title' => 'Verbs', 'level' => 2, 'parent_id' => 1),
            array('title' => 'Adverbs', 'level' => 2, 'parent_id' => 1),
            array('title' => 'Counting', 'level' => 2, 'parent_id' => 2),
            array('title' => 'Addition', 'level' => 2, 'parent_id' => 2),
            array('title' => 'Subtraction', 'level' => 2, 'parent_id' => 2),
            array('title' => 'Multiplication', 'level' => 2, 'parent_id' => 2),
            array('title' => 'Division', 'level' => 2, 'parent_id' => 2),
            array('title' => 'Physics', 'level' => 2, 'parent_id' => 3),
            array('title' => 'Chemistry', 'level' => 2, 'parent_id' => 3),
            array('title' => 'Biology', 'level' => 2, 'parent_id' => 3),
            array('title' => 'Basics', 'level' => 2, 'parent_id' => 4),
            array('title' => 'C++', 'level' => 2, 'parent_id' => 4),
            array('title' => 'Java', 'level' => 2, 'parent_id' => 4),
            array('title' => 'Python', 'level' => 2, 'parent_id' => 4),
        );

        DB::table('categories')->insert($currencies);
    }
}
