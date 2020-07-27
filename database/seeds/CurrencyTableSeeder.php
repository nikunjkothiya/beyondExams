<?php

use Illuminate\Database\Seeder;

class CurrencyTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $currencies = array(
			array('name' => 'USD', 'symbol' => '$'),
			array('name' => 'INR', 'symbol' => '₹'),
		);
		DB::table('currencies')->insert($currencies);
    }
}
