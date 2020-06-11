<?php

use Illuminate\Database\Seeder;

class ActionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $actions = array(
            array('event' => 'Read more'),
            array('event' => 'Share'),
            array('event' => 'Official Link'),
            array('event' => 'Save Opp'),
            array('event' => 'Reminder'),
            array('event' => 'Relevant'),
            array('event' => 'Views')
        );
        DB::table('actions')->insert($actions);
    }
}
