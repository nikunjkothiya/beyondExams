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
        $this->call(realAnalyticsTableSeeder::class);
        $this->call(realFileTypesTableSeeder::class);
        $this->call(realLegacyUsersTableSeeder::class);
        $this->call(realUsersTableSeeder::class);
        $this->call(realUserSocialsTableSeeder::class);
        $this->call(realUserDetailsTableSeeder::class);
        $this->call(realListCommentsTableSeeder::class);
        $this->call(realMentorDetailsTableSeeder::class);
        $this->call(realMentorVerificationsTableSeeder::class);
        $this->call(realResourcesTableSeeder::class);
        $this->call(realOauthAccessTokensTableSeeder::class);
        $this->call(realOauthAuthCodesTableSeeder::class);
        $this->call(realOauthClientsTableSeeder::class);
        $this->call(realOauthPersonalAccessClientsTableSeeder::class);
        $this->call(realOauthRefreshTokensTableSeeder::class);
        $this->call(realLegacyOpportunitiesTableSeeder::class);
        $this->call(realOpportunitiesTableSeeder::class);
        $this->call(realOrganisationsTableSeeder::class);
        $this->call(realOrganisationSocialsTableSeeder::class);
        $this->call(realOrganisationDetailsTableSeeder::class);
        $this->call(realEligibleRegionOpportunityTableSeeder::class);
        $this->call(realOpportunityCommentsTableSeeder::class);
        $this->call(realOpportunityOrganisationTableSeeder::class);
        $this->call(realOpportunityTagTableSeeder::class);
        $this->call(realOpportunityTranslationsTableSeeder::class);
        $this->call(realOpportunityUserTableSeeder::class);
        $this->call(realPasswordResetsTableSeeder::class);
        $this->call(realPlusTransactionsTableSeeder::class);
        $this->call(realUserCommentsTableSeeder::class);
        $this->call(realUserRolesTableSeeder::class);
        $this->call(realReplyTableSeeder::class);
        $this->call(realTagUserTableSeeder::class);
        $this->call(realTransactionsTableSeeder::class);
        $this->call(CurrencyTableSeeder::class);
    }
}
