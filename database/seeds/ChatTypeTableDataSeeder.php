<?php

use Illuminate\Database\Seeder;
use App\ChatType;

class ChatTypeTableDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $chat_type = [
            ['id' => 1, 'type' => 'normal','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'),],
            ['id' => 2, 'type' => 'support','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'),],
            ['id' => 3, 'type' => 'whatsapp','created_at' => date('Y-m-d H:i:s'),'updated_at' => date('Y-m-d H:i:s'),],
        ];
		DB::table('chat_types')->insert($chat_type);
    }
}
