<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            array('code' => 'bn' ,'language' => 'বাংলা', 'language_example' => 'bn'),
            array('code' => 'de' ,'language' => 'Deutsch', 'language_example' => 'de'),
            array('code' => 'en' ,'language' => 'English', 'language_example' => 'en'),
            array('code' => 'es' ,'language' => 'Español', 'language_example' => 'es'),
            array('code' => 'fr' ,'language' => 'Français', 'language_example' => 'fr'),
            array('code' => 'hi' ,'language' => 'हिन्दी', 'language_example' => 'hi'),
            array('code' => 'id' ,'language' => 'Bahasa Indonesia', 'language_example' => 'id'),
            array('code' => 'it' ,'language' => 'Italiano', 'language_example' => 'it'),
            array('code' => 'ja' ,'language' => '日本語', 'language_example' => 'ja'),
            array('code' => 'km' ,'language' => 'ភាសាខ្មែរ', 'language_example' => 'km'),
            array('code' => 'ko' ,'language' => ' 	한국어', 'language_example' => 'ko'),
            array('code' => 'lo' ,'language' => 'ພາສາລາວ', 'language_example' => 'lo'),
            array('code' => 'ms' ,'language' => 'بهاس ملايو', 'language_example' => 'ms'),
            array('code' => 'my' ,'language' => 'ဗမာစာ', 'language_example' => 'my'),
            array('code' => 'ne' ,'language' => 'नेपाली', 'language_example' => 'ne'),
            array('code' => 'ro' ,'language' => 'Română', 'language_example' => 'ro'),
            array('code' => 'ru' ,'language' => 'русский', 'language_example' => 'ru'),
            array('code' => 'si' ,'language' => 'සිංහල', 'language_example' => 'si'),
            array('code' => 'ta' ,'language' => 'தமிழ்', 'language_example' => 'ta'),
            array('code' => 'th' ,'language' => 'ไทย', 'language_example' => 'th'),
            array('code' => 'tl' ,'language' => 'Wikang Tagalog', 'language_example' => 'tl'),
            array('code' => 'vi' ,'language' => 'Tiếng Việt', 'language_example' => 'vi'),
            array('code' => 'zh' ,'language' => '中文', 'language_example' => 'zh'),
		);
		DB::table('languages')->insert($languages);
    }
}
