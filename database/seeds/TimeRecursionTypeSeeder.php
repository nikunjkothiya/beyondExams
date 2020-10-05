<?php

use Illuminate\Database\Seeder;

class TimeRecursionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions = array(
            array('repeat_type' => 'Once'),
            array('repeat_type' => 'Daily'),
            array('repeat_type' => 'Specific days of week'),
            array('repeat_type' => 'weekly'),
            array('repeat_type' => 'monthly'),
            array('repeat_type' => 'yearly'),
        );
        DB::table('time_recursion_types')->insert($actions);
    }
}
