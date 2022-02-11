<?php

namespace Database\Seeders;

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
        $this->call([
            ModuleTableSeeder::class,
            SubModuleTableSeeder::class,
            RoleTableSeeder::class,
            PermissionTableSeeder::class,
            CompaniesTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            CountriesTableSeeder::class,
            Department1TableSeeder::class,
            MaximumOSTableSeeder::class,
            NontaxableIncomeTableSeeder::class,
            TaxByGoverntmentTableSeeder::class,
            TaxTariffTableSeeder::class,
            TaxTableSeeder::class,
            BpjsTableSeeder::class,
            GradesTableSeeder::class,
            JobTitleTableSeeder::class,
            LocationsTableSeeder::class,
            TeamsTableSeeder::class,
            UsersTableSeeder::class,
            NewsTagsSeeder::class,
            NewsFeedSeeder::class,
        ]);
    }
}
