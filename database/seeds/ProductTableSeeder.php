<?php

use Illuminate\Database\Seeder;

class ProductTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $products = array(
			array('name' => 'Premium'),
			array('name' => 'Resource Key'),
		);
		DB::table('products')->insert($products);
    }
}
