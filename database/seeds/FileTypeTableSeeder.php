<?php

use Illuminate\Database\Seeder;

class FileTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = array(
            array('type' => 'blogs'),
            array('type' => 'articles'),
            array('type' => 'videos'),
        );
        DB::table('file_types')->insert($types);
    }
}
