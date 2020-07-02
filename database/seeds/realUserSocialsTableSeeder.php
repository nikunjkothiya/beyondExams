<?php

use Illuminate\Database\Seeder;

class realUserSocialsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('user_socials')->delete();
        
        \DB::table('user_socials')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => 2,
                'provider' => 'facebook',
                'provider_id' => '2939552116090967',
                'created_at' => '2020-06-02 21:07:46',
                'updated_at' => '2020-06-02 21:07:46',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => 3,
                'provider' => 'google',
                'provider_id' => '109685012718120664875',
                'created_at' => '2020-06-02 21:10:40',
                'updated_at' => '2020-06-02 21:10:40',
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => 4,
                'provider' => 'google',
                'provider_id' => '102142826004744395267',
                'created_at' => '2020-06-02 21:11:25',
                'updated_at' => '2020-06-02 21:11:25',
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => 5,
                'provider' => 'google',
                'provider_id' => '105245471394466799213',
                'created_at' => '2020-06-02 21:12:27',
                'updated_at' => '2020-06-02 21:12:27',
            ),
            4 => 
            array (
                'id' => 5,
                'user_id' => 6,
                'provider' => 'google',
                'provider_id' => '108681038809854628624',
                'created_at' => '2020-06-03 08:11:50',
                'updated_at' => '2020-06-03 08:11:50',
            ),
            5 => 
            array (
                'id' => 6,
                'user_id' => 7,
                'provider' => 'google',
                'provider_id' => '110408958663856672082',
                'created_at' => '2020-06-03 08:14:14',
                'updated_at' => '2020-06-03 08:14:14',
            ),
            6 => 
            array (
                'id' => 7,
                'user_id' => 8,
                'provider' => 'google',
                'provider_id' => '107819691307628445242',
                'created_at' => '2020-06-03 12:23:57',
                'updated_at' => '2020-06-03 12:23:57',
            ),
            7 => 
            array (
                'id' => 8,
                'user_id' => 9,
                'provider' => 'google',
                'provider_id' => '113960673074285438716',
                'created_at' => '2020-06-05 09:34:07',
                'updated_at' => '2020-06-05 09:34:07',
            ),
            8 => 
            array (
                'id' => 9,
                'user_id' => 10,
                'provider' => 'google',
                'provider_id' => '114730737280378175174',
                'created_at' => '2020-06-10 10:22:46',
                'updated_at' => '2020-06-10 10:22:46',
            ),
            9 => 
            array (
                'id' => 10,
                'user_id' => 11,
                'provider' => 'google',
                'provider_id' => '110566000701862503243',
                'created_at' => '2020-06-10 20:04:32',
                'updated_at' => '2020-06-10 20:04:32',
            ),
            10 => 
            array (
                'id' => 11,
                'user_id' => 12,
                'provider' => 'google',
                'provider_id' => '113154966015981683814',
                'created_at' => '2020-06-10 22:29:58',
                'updated_at' => '2020-06-10 22:29:58',
            ),
            11 => 
            array (
                'id' => 12,
                'user_id' => 13,
                'provider' => 'google',
                'provider_id' => '101114778992508840605',
                'created_at' => '2020-06-11 09:12:15',
                'updated_at' => '2020-06-11 09:12:15',
            ),
            12 => 
            array (
                'id' => 13,
                'user_id' => 14,
                'provider' => 'google',
                'provider_id' => '118342987765208773222',
                'created_at' => '2020-06-11 09:15:32',
                'updated_at' => '2020-06-11 09:15:32',
            ),
            13 => 
            array (
                'id' => 14,
                'user_id' => 15,
                'provider' => 'google',
                'provider_id' => '104573490927005313320',
                'created_at' => '2020-06-11 19:20:07',
                'updated_at' => '2020-06-11 19:20:07',
            ),
            14 => 
            array (
                'id' => 15,
                'user_id' => 16,
                'provider' => 'google',
                'provider_id' => '110277121225297520867',
                'created_at' => '2020-06-12 09:29:41',
                'updated_at' => '2020-06-12 09:29:41',
            ),
            15 => 
            array (
                'id' => 16,
                'user_id' => 17,
                'provider' => 'google',
                'provider_id' => '116278970005385931198',
                'created_at' => '2020-06-12 17:58:30',
                'updated_at' => '2020-06-12 17:58:30',
            ),
            16 => 
            array (
                'id' => 17,
                'user_id' => 18,
                'provider' => 'google',
                'provider_id' => '100528647563801761674',
                'created_at' => '2020-06-12 18:00:29',
                'updated_at' => '2020-06-12 18:00:29',
            ),
            17 => 
            array (
                'id' => 18,
                'user_id' => 19,
                'provider' => 'google',
                'provider_id' => '111380935974171658812',
                'created_at' => '2020-06-12 18:24:20',
                'updated_at' => '2020-06-12 18:24:20',
            ),
            18 => 
            array (
                'id' => 19,
                'user_id' => 20,
                'provider' => 'google',
                'provider_id' => '113336546628353205459',
                'created_at' => '2020-06-13 11:26:40',
                'updated_at' => '2020-06-13 11:26:40',
            ),
            19 => 
            array (
                'id' => 20,
                'user_id' => 21,
                'provider' => 'google',
                'provider_id' => '117009533880755865001',
                'created_at' => '2020-06-13 12:21:41',
                'updated_at' => '2020-06-13 12:21:41',
            ),
            20 => 
            array (
                'id' => 21,
                'user_id' => 22,
                'provider' => 'google',
                'provider_id' => '103875978111189600019',
                'created_at' => '2020-06-13 12:46:02',
                'updated_at' => '2020-06-13 12:46:02',
            ),
            21 => 
            array (
                'id' => 22,
                'user_id' => 23,
                'provider' => 'google',
                'provider_id' => '113320750981768624235',
                'created_at' => '2020-06-14 08:50:47',
                'updated_at' => '2020-06-14 08:50:47',
            ),
            22 => 
            array (
                'id' => 23,
                'user_id' => 24,
                'provider' => 'google',
                'provider_id' => '111104617802463035969',
                'created_at' => '2020-06-14 13:26:06',
                'updated_at' => '2020-06-14 13:26:06',
            ),
            23 => 
            array (
                'id' => 24,
                'user_id' => 25,
                'provider' => 'google',
                'provider_id' => '115564064410559687972',
                'created_at' => '2020-06-14 18:58:38',
                'updated_at' => '2020-06-14 18:58:38',
            ),
            24 => 
            array (
                'id' => 25,
                'user_id' => 26,
                'provider' => 'google',
                'provider_id' => '107070302990004414923',
                'created_at' => '2020-06-14 19:01:40',
                'updated_at' => '2020-06-14 19:01:40',
            ),
            25 => 
            array (
                'id' => 26,
                'user_id' => 27,
                'provider' => 'google',
                'provider_id' => '110977329937258611357',
                'created_at' => '2020-06-16 20:19:51',
                'updated_at' => '2020-06-16 20:19:51',
            ),
            26 => 
            array (
                'id' => 27,
                'user_id' => 28,
                'provider' => 'google',
                'provider_id' => '116880210835577894192',
                'created_at' => '2020-06-16 20:44:43',
                'updated_at' => '2020-06-16 20:44:43',
            ),
            27 => 
            array (
                'id' => 28,
                'user_id' => 29,
                'provider' => 'google',
                'provider_id' => '111754785430730908203',
                'created_at' => '2020-06-18 15:55:22',
                'updated_at' => '2020-06-18 15:55:22',
            ),
            28 => 
            array (
                'id' => 29,
                'user_id' => 30,
                'provider' => 'google',
                'provider_id' => '109957516553947887691',
                'created_at' => '2020-06-18 18:58:37',
                'updated_at' => '2020-06-18 18:58:37',
            ),
        ));
        
        
    }
}