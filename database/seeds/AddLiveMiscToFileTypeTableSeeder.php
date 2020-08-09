<?php

use Illuminate\Database\Seeder;

class AddLiveMiscToFileTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $types = array(
            array('type' => 'live'),
            array('type' => 'misc')
        );

        DB::table('file_types')->insert($types);
    }
}
