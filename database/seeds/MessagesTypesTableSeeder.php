<?php

use Illuminate\Database\Seeder;

class MessagesTypesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $messageTypes = array(
			array('type' => 'text'),
			array('type' => 'image'),
			array('type' => 'video'),
			array('type' => 'audio'),
			array('type' => 'document'),
        );
		DB::table('message_types')->insert($messageTypes);
    }
}
