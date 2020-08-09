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
//        DB::table('file_types')->delete();

        $types = array(
            array('type' => 'blogs'),
            array('type' => 'articles'),
            array('type' => 'videos'),
            array('type' => 'playlist'),
        );

        DB::table('file_types')->insert($types);
    }
}
