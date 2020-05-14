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
            array('type' => 'video'),
            array('type' => 'document'),
            array('type' => 'audio'),
            array('type' => 'image'),
        );
        DB::table('file_types')->insert($types);
    }
}
