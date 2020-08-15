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
        $categories = array(
            array('name' => 'cold'),
            array('name' => 'normal'),
            array('name' => 'warm'),
            array('name' => 'hot')
        );
		DB::table('categories')->insert($categories);
    }
}
