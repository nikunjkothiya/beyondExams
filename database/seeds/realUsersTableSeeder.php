<?php

use Illuminate\Database\Seeder;

class realUsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('users')->delete();
        
        \DB::table('users')->insert(array (
            0 => 
            array (
                'id' => 1,
                'name' => 'Precisely',
                'unique_id' => '123456789',
                'email' => 'admin@precisely.co.in',
                'role_id' => 3,
                'password' => '$2y$10$siRj3pp.G8InEIDYfqKM7e3ml2NTG8b/T0bEchTbPXq4fbZGyZwcC',
                'avatar' => 'https://www.precisely.co.in/assets/images/logo.png',
                'remember_token' => NULL,
                'created_at' => NULL,
                'updated_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'name' => 'Pankaj Baranwal',
                'unique_id' => '2939552116090967',
                'email' => 'pankajbaranwal.1996@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://graph.facebook.com/v3.3/2939552116090967/picture?type=normal',
                'remember_token' => NULL,
                'created_at' => '2020-06-02 21:07:46',
                'updated_at' => '2020-06-02 21:07:46',
            ),
            2 => 
            array (
                'id' => 3,
                'name' => 'Pankaj Baranwal',
                'unique_id' => '109685012718120664875',
                'email' => 'pankajbaranwal.1996@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14Ghf56ReK7owuGyi-aLRzkx__YmjpEdhryhOUHKQrQ',
                'remember_token' => NULL,
                'created_at' => '2020-06-02 21:10:40',
                'updated_at' => '2020-06-02 21:10:40',
            ),
            3 => 
            array (
                'id' => 4,
                'name' => 'PANKAJ BARANWAL',
                'unique_id' => '102142826004744395267',
                'email' => 'pankaj11520@ducic.ac.in',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh5.googleusercontent.com/-OSxcb_Im2P8/AAAAAAAAAAI/AAAAAAAAAAA/AMZuucln59C9FdN9VDrKHmvHL8snhfRZyA/photo.jpg',
                'remember_token' => NULL,
                'created_at' => '2020-06-02 21:11:25',
                'updated_at' => '2020-06-02 21:11:25',
            ),
            4 => 
            array (
                'id' => 5,
                'name' => 'Hitesh Gautam',
                'unique_id' => '105245471394466799213',
                'email' => 'gautam31.hitesh@ducic.ac.in',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GigdecgWyfJhxLazYwB6Oz5-VCGq7gH0M27A_qj',
                'remember_token' => NULL,
                'created_at' => '2020-06-02 21:12:27',
                'updated_at' => '2020-06-02 21:12:27',
            ),
            5 => 
            array (
                'id' => 6,
                'name' => 'Raghav Dhingra',
                'unique_id' => '108681038809854628624',
                'email' => 'raghav.dhingra15@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GhhIy3hCsTSKScICSioBMV_4p7xcLGPldv2D9Vjpw',
                'remember_token' => NULL,
                'created_at' => '2020-06-03 08:11:50',
                'updated_at' => '2020-06-03 08:11:50',
            ),
            6 => 
            array (
                'id' => 7,
                'name' => 'JUST ABOUT TECH',
                'unique_id' => '110408958663856672082',
                'email' => 'justabouttech15@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GiFT_GbxwzUkuCdvOK2BwRxBhPxR4LSCU4Wp9n5',
                'remember_token' => NULL,
                'created_at' => '2020-06-03 08:14:14',
                'updated_at' => '2020-06-03 08:14:14',
            ),
            7 => 
            array (
                'id' => 8,
                'name' => 'Hitesh Gautam',
                'unique_id' => '107819691307628445242',
                'email' => 'gautam31.hitesh@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GhzUg35k_N0oYpaBTredW3-2NJDqQFHPBhdx4ETKw',
                'remember_token' => NULL,
                'created_at' => '2020-06-03 12:23:57',
                'updated_at' => '2020-06-03 12:23:57',
            ),
            8 => 
            array (
                'id' => 9,
                'name' => 'Nikhil Kadyan',
                'unique_id' => '113960673074285438716',
                'email' => 'nikhilkadyan000@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GiKrmV5tLbHuyRqLWD7UGmQsBHTUyE5c4c5L-Mq',
                'remember_token' => NULL,
                'created_at' => '2020-06-05 09:34:07',
                'updated_at' => '2020-06-05 09:34:07',
            ),
            9 => 
            array (
                'id' => 10,
                'name' => 'Chetan Kashyap',
                'unique_id' => '114730737280378175174',
                'email' => 'chetan.kashyap7497@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh5.googleusercontent.com/-XYtwPfBb3rU/AAAAAAAAAAI/AAAAAAAAAAA/AMZuuckBXsLv3F8mtHqpfgvPcr4zLsMSMQ/photo.jpg',
                'remember_token' => NULL,
                'created_at' => '2020-06-10 10:22:46',
                'updated_at' => '2020-06-10 10:22:46',
            ),
            10 => 
            array (
                'id' => 11,
                'name' => 'Rahul Maurya',
                'unique_id' => '110566000701862503243',
                'email' => 'maurya.rahul111@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GhtdcNDTngqhSx3knl2CRr-G_VWtGBAJO_PKS7Pi2c',
                'remember_token' => NULL,
                'created_at' => '2020-06-10 20:04:32',
                'updated_at' => '2020-06-10 20:04:32',
            ),
            11 => 
            array (
                'id' => 12,
                'name' => 'maninder singh',
                'unique_id' => '113154966015981683814',
                'email' => 'maninderstar1998@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/-sSV_F3LqeJI/AAAAAAAAAAI/AAAAAAAAAAA/AMZuucnrOlVVbHDTT_C9XVn5wm_Uic9yyg/photo.jpg',
                'remember_token' => NULL,
                'created_at' => '2020-06-10 22:29:58',
                'updated_at' => '2020-06-10 22:29:58',
            ),
            12 => 
            array (
                'id' => 13,
                'name' => 'elsya angkawati',
                'unique_id' => '101114778992508840605',
                'email' => 'elsya.angkawati@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh4.googleusercontent.com/-cbmnlOTTIGI/AAAAAAAAAAI/AAAAAAAAAAA/AMZuucnHDlBybYXHLKs8i_ES_zWvFIWYTg/photo.jpg',
                'remember_token' => NULL,
                'created_at' => '2020-06-11 09:12:15',
                'updated_at' => '2020-06-11 09:12:15',
            ),
            13 => 
            array (
                'id' => 14,
                'name' => 'El',
                'unique_id' => '118342987765208773222',
                'email' => 'elswordayodance@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GghRwyUK_13qgGW6en6E_hbfyLaPYkNvSaz2czd',
                'remember_token' => NULL,
                'created_at' => '2020-06-11 09:15:32',
                'updated_at' => '2020-06-11 09:15:32',
            ),
            14 => 
            array (
                'id' => 15,
                'name' => 'Abdo Bode',
                'unique_id' => '104573490927005313320',
                'email' => 'bodeabdo402@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh4.googleusercontent.com/-Z5fY_VZL6EE/AAAAAAAAAAI/AAAAAAAAAAA/AMZuuclhpuNMKkzMC5HsdVsJMHbEgp0nmA/photo.jpg',
                'remember_token' => NULL,
                'created_at' => '2020-06-11 19:20:07',
                'updated_at' => '2020-06-11 19:20:07',
            ),
            15 => 
            array (
                'id' => 16,
                'name' => 'Kirti Krishan',
                'unique_id' => '110277121225297520867',
                'email' => 'krishankirti9@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14Gg6OM-rREwb8vEVUrS3Akts3DizoE-BTcBIO084RKk',
                'remember_token' => NULL,
                'created_at' => '2020-06-12 09:29:41',
                'updated_at' => '2020-06-12 09:29:41',
            ),
            16 => 
            array (
                'id' => 17,
                'name' => 'Hitesh Gautam',
                'unique_id' => '116278970005385931198',
                'email' => 'gautam14.hitesh@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GiOe3vx_Un9CDUlRYOMiVAcGs85nWhR7dqilWnWPw',
                'remember_token' => NULL,
                'created_at' => '2020-06-12 17:58:30',
                'updated_at' => '2020-06-12 17:58:30',
            ),
            17 => 
            array (
                'id' => 18,
                'name' => 'Lakshay Juneja',
                'unique_id' => '100528647563801761674',
                'email' => 'workwithlakshay@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GiseBz70_IBp_Jysf1F72vsMZKyG6nl8ELb4yo',
                'remember_token' => NULL,
                'created_at' => '2020-06-12 18:00:29',
                'updated_at' => '2020-06-12 18:00:29',
            ),
            18 => 
            array (
                'id' => 19,
                'name' => 'Hiring Trial',
                'unique_id' => '111380935974171658812',
                'email' => 'hiringtrial@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh5.googleusercontent.com/-GeNP_IvslQI/AAAAAAAAAAI/AAAAAAAAAAA/AMZuucmZlwAYkLM5SFXtoW7lM7DQOC8ZnA/photo.jpg',
                'remember_token' => NULL,
                'created_at' => '2020-06-12 18:24:20',
                'updated_at' => '2020-06-12 18:24:20',
            ),
            19 => 
            array (
                'id' => 20,
                'name' => 'Kirti Krishan',
                'unique_id' => '113336546628353205459',
                'email' => 'kirti21520@ducic.ac.in',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GiW66e4OMiziK9_8OR72fxPSrt9gVUfuvarzS7A',
                'remember_token' => NULL,
                'created_at' => '2020-06-13 11:26:40',
                'updated_at' => '2020-06-13 11:26:40',
            ),
            20 => 
            array (
                'id' => 21,
                'name' => 'Desh Dwivedi',
                'unique_id' => '117009533880755865001',
                'email' => 'dwivedidesh@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GjiNGkgRvg46iwIndH2644p4t9OCp0JeD6fbqpvf0s',
                'remember_token' => NULL,
                'created_at' => '2020-06-13 12:21:41',
                'updated_at' => '2020-06-13 12:21:41',
            ),
            21 => 
            array (
                'id' => 22,
                'name' => 'Orendra Singh',
                'unique_id' => '103875978111189600019',
                'email' => 'orendrasingh@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GjnCP1nE7A_gH59ek-38sYkSaMjS8ej7n6RnlhOT_g',
                'remember_token' => NULL,
                'created_at' => '2020-06-13 12:46:02',
                'updated_at' => '2020-06-13 12:46:02',
            ),
            22 => 
            array (
                'id' => 23,
                'name' => 'Kumbukani Joabe',
                'unique_id' => '113320750981768624235',
                'email' => 'kjoabe02@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh4.googleusercontent.com/-0S-eDQsGVOU/AAAAAAAAAAI/AAAAAAAAAAA/AMZuucn-m6VnfnW8IBAMKhO_KWvEofiQ6g/photo.jpg',
                'remember_token' => NULL,
                'created_at' => '2020-06-14 08:50:47',
                'updated_at' => '2020-06-14 08:50:47',
            ),
            23 => 
            array (
                'id' => 24,
                'name' => 'Gurkaran Singh',
                'unique_id' => '111104617802463035969',
                'email' => 'sgurkaran2000@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh4.googleusercontent.com/-A7VsAzM6G-o/AAAAAAAAAAI/AAAAAAAAAAA/AMZuucl06-p5y-dtCfEAzuh0o7hoO3WQjA/photo.jpg',
                'remember_token' => NULL,
                'created_at' => '2020-06-14 13:26:06',
                'updated_at' => '2020-06-14 13:26:06',
            ),
            24 => 
            array (
                'id' => 25,
                'name' => 'Akshit Kr Nagpal',
                'unique_id' => '115564064410559687972',
                'email' => 'nagpalkrakshit@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GhmKHw5_bR1ufhX9xJl-1-2kgehqVniIhAuFsmLtw',
                'remember_token' => NULL,
                'created_at' => '2020-06-14 18:58:38',
                'updated_at' => '2020-06-14 18:58:38',
            ),
            25 => 
            array (
                'id' => 26,
                'name' => 'Akshit Kr Nagpal',
                'unique_id' => '107070302990004414923',
                'email' => 'akshitkrnagpal@ducic.ac.in',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GjpMwlW5yDPPD2Y5a7JSEGS11Dk8aWE-TEnvhtA',
                'remember_token' => NULL,
                'created_at' => '2020-06-14 19:01:40',
                'updated_at' => '2020-06-14 19:01:40',
            ),
            26 => 
            array (
                'id' => 27,
                'name' => 'Your Teacher',
                'unique_id' => '110977329937258611357',
                'email' => 'yourteacher.ml@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh5.googleusercontent.com/-si9xwl3SPrc/AAAAAAAAAAI/AAAAAAAAAAA/AMZuucl1JoVRThV6Pzguvwa9zAg31WQWqA/photo.jpg',
                'remember_token' => NULL,
                'created_at' => '2020-06-16 20:19:51',
                'updated_at' => '2020-06-16 20:19:51',
            ),
            27 => 
            array (
                'id' => 28,
                'name' => 'I Stan Girlgroups',
                'unique_id' => '116880210835577894192',
                'email' => 'yasminaziz2004@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14Ggvat54zS0YSXU8xxcLTGZY24C9oC_u5-iP78T3IA',
                'remember_token' => NULL,
                'created_at' => '2020-06-16 20:44:43',
                'updated_at' => '2020-06-16 20:44:43',
            ),
            28 => 
            array (
                'id' => 29,
                'name' => 'gtbit backend',
                'unique_id' => '111754785430730908203',
                'email' => 'gtbitbackend@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/-vYhpd_ALnnI/AAAAAAAAAAI/AAAAAAAAAAA/AMZuucmeMsKd56eX2B_d5Lr70t1uwYq2NQ/photo.jpg',
                'remember_token' => NULL,
                'created_at' => '2020-06-18 15:55:22',
                'updated_at' => '2020-06-18 15:55:22',
            ),
            29 => 
            array (
                'id' => 30,
                'name' => 'fernanda rojas',
                'unique_id' => '109957516553947887691',
                'email' => 'rojasfernanda201@gmail.com',
                'role_id' => 1,
                'password' => NULL,
                'avatar' => 'https://lh3.googleusercontent.com/a-/AOh14GgLeVTFfq3SWakPcBVr-t_2qVMSYiQVNroXPCcWqw',
                'remember_token' => NULL,
                'created_at' => '2020-06-18 18:58:37',
                'updated_at' => '2020-06-18 18:58:37',
            ),
        ));
        
        
    }
}