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
            AccessModulesTableSeeder::class,
            PermissionTableSeeder::class,
            PermissionRoleTableSeeder::class,
            UsersTableSeeder::class,
            RoleUserTableSeeder::class,
            TaskTableSeeder::class,
        ]);
    }
}
