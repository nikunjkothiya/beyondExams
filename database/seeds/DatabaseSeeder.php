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
        $this->call(DisciplineTableSeeder::class);
        $this->call(FundTypeTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(OpportunityLocationTableSeeder::class);
        $this->call(EligibleRegionTableSeeder::class);
        $this->call(ProductTableSeeder::class);
        $this->call(QualificationTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(TagTableSeeder::class);
        $this->call(OpportunitiesTableSeeder::class);
    }
}
