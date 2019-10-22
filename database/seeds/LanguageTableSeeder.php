<?php

use Illuminate\Database\Seeder;

class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $languages = array(
			array('code' => 'bn' ,'language' => 'বাংলা'),
			array('code' => 'de' ,'language' => 'Deutsch'),
			array('code' => 'en' ,'language' => 'English'),
			array('code' => 'es' ,'language' => 'Español'),
			array('code' => 'fr' ,'language' => 'Français'),
			array('code' => 'hi' ,'language' => 'हिन्दी'),
			array('code' => 'id' ,'language' => 'Bahasa Indonesia'),
			array('code' => 'it' ,'language' => 'Italiano'),
			array('code' => 'ja' ,'language' => '日本語'),
			array('code' => 'km' ,'language' => 'ភាសាខ្មែរ'),
			array('code' => 'ko' ,'language' => ' 	한국어'),
			array('code' => 'lo' ,'language' => 'ພາສາລາວ'),
			array('code' => 'ms' ,'language' => 'بهاس ملايو'),
			array('code' => 'my' ,'language' => 'ဗမာစာ'),
			array('code' => 'ne' ,'language' => 'नेपाली'),
			array('code' => 'ro' ,'language' => 'Română'),
			array('code' => 'ru' ,'language' => 'русский'),
			array('code' => 'si' ,'language' => 'සිංහල'),
			array('code' => 'ta' ,'language' => 'தமிழ்'),
			array('code' => 'th' ,'language' => 'ไทย'),
			array('code' => 'tl' ,'language' => 'Wikang Tagalog'),
			array('code' => 'vi' ,'language' => 'Tiếng Việt'),
			array('code' => 'zh' ,'language' => '中文'),
		);
		DB::table('languages')->insert($languages);
    }
}
