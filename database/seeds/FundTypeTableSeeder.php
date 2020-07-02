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
			array('type' => 'Fully Funded'),
			array('type' => 'Partially Funded'),
			array('type' => 'NA'),
		);
		DB::table('fund_types')->insert($fundtypes);
    }
}
