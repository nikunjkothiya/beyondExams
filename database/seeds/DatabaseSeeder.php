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
        $this->call(FundTypeTableSeeder::class);
        $this->call(FileTypeTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(OpportunityLocationTableSeeder::class);
        $this->call(EligibleRegionTableSeeder::class);
        $this->call(ProductTableSeeder::class);
        $this->call(QualificationsTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(TagTypeTableSeeder::class);
        $this->call(TagTableSeeder::class);
        $this->call(OpportunitiesTableSeeder::class);
        $this->call(OrganisationTableSeeder::class);
        $this->call(UserTableSeeder::class);
    }
}
