<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccessModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('access_modules')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('access_modules')->insert([
            [
                'am_id'         => 1,
                'mod_id'        => 1,
                'role_id'       => 1,
                'am_rights'     => 1,
            ],
            [
                'am_id'         => 2,
                'mod_id'        => 2,
                'role_id'       => 1,
                'am_rights'     => 1,
            ],
            [
                'am_id'         => 3,
                'mod_id'        => 1,
                'role_id'       => 2,
                'am_rights'     => 1,
            ],
            [
                'am_id'         => 4,
                'mod_id'        => 2,
                'role_id'       => 2,
                'am_rights'     => 1,
            ],
        ]);
    }
}
