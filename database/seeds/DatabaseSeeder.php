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
        $this->call(CategoryTableSeeder::class);
        $this->call(DisciplinesTableSeeder::class);
        $this->call(FileTypeTableSeeder::class);
        $this->call(MessagesTypesTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(EligibleRegionTableSeeder::class);
        $this->call(QualificationsTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(TagTypeTableSeeder::class);
        $this->call(TagTableSeeder::class);
        $this->call(DomainTableSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(realAnalyticsTableSeeder::class);
        $this->call(realFileTypesTableSeeder::class);
        $this->call(realUsersTableSeeder::class);
        $this->call(realUserSocialsTableSeeder::class);
        $this->call(realListCommentsTableSeeder::class);
        $this->call(realMentorVerificationsTableSeeder::class);
        $this->call(realResourcesTableSeeder::class);
        $this->call(realOauthAccessTokensTableSeeder::class);
        $this->call(realOauthAuthCodesTableSeeder::class);
        $this->call(realOauthClientsTableSeeder::class);
        $this->call(realOauthPersonalAccessClientsTableSeeder::class);
        $this->call(realOauthRefreshTokensTableSeeder::class);
        $this->call(realPasswordResetsTableSeeder::class);
        $this->call(realUserCommentsTableSeeder::class);
        $this->call(realUserRolesTableSeeder::class);
        $this->call(realReplyTableSeeder::class);
        $this->call(realTagUserTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
    }
}
