<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CountryTableSeeder::class);
        $this->call(DisciplinesTableSeeder::class);
        $this->call(MessagesTypesTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(QualificationsTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(TagTypeTableSeeder::class);
        $this->call(TagTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
    }
}
