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
			array('name' => 'Try for a month', 'price' => 0.00,'months' => 1),
			array('name' => '3 months', 'price' => 249.00,'months' => 3),
			array('name' => '7 months', 'price' => 499.00,'months' => 7),
			array('name' => '12 months', 'price' => 999.00,'months' => 12),
		);
		DB::table('products')->insert($products);
    }
}
