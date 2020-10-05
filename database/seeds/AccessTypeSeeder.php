<?php

use Illuminate\Database\Seeder;

class AccessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions = array(
            array('name' => 'Private'),
            array('name' => 'Public'),
        );
        DB::table('access_types')->insert($actions);
    }
}
