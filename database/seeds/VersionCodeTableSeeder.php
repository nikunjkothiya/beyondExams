<?php

use Illuminate\Database\Seeder;

class VersionCodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $version_codes = array(
			array('version_code' => '100'),
			array('version_code' => '37'),
			array('version_code' => '35'),
			array('version_code' => '38'),
			array('version_code' => '38'),
			array('version_code' => '39'),
			array('version_code' => '40'),
			array('version_code' => '34'),
			array('version_code' => '36'),
		);
		DB::table('version_codes')->insert($version_codes);
    }
}
