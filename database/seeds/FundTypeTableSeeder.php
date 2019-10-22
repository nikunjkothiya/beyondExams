<?php

use Illuminate\Database\Seeder;

class FundTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fundtypes = array(
			array('type' => 'Fully'),
			array('type' => 'Partially'),
			array('type' => 'NA'),
		);
		DB::table('fund_types')->insert($fundtypes);
    }
}
