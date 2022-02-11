<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AccessModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('access_modules')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \DB::table('access_modules')->insert([
            [
                'am_id'         => 1,
                'mod_id'        => 1,
                'role_id'       => 3,
                'am_rights'     => 1,
            ],
            [
                'am_id'         => 2,
                'mod_id'        => 2,
                'role_id'       => 3,
                'am_rights'     => 1,
            ],
            [
                'am_id'         => 3,
                'mod_id'        => 3,
                'role_id'       => 3,
                'am_rights'     => 1,
            ],
            [
                'am_id'         => 4,
                'mod_id'        => 4,
                'role_id'       => 3,
                'am_rights'     => 1,
            ],
            [
                'am_id'         => 5,
                'mod_id'        => 5,
                'role_id'       => 3,
                'am_rights'     => 1,
            ],
            [
                'am_id'         => 6,
                'mod_id'        => 6,
                'role_id'       => 3,
                'am_rights'     => 1,
            ],
            [
                'am_id'         => 7,
                'mod_id'        => 1,
                'role_id'       => 4,
                'am_rights'     => 2,
            ],
            [
                'am_id'         => 8,
                'mod_id'        => 2,
                'role_id'       => 4,
                'am_rights'     => 2,
            ],
            [
                'am_id'         => 9,
                'mod_id'        => 3,
                'role_id'       => 4,
                'am_rights'     => 2,
            ],
            [
                'am_id'         => 10,
                'mod_id'        => 4,
                'role_id'       => 4,
                'am_rights'     => 2,
            ],
            [
                'am_id'         => 11,
                'mod_id'        => 5,
                'role_id'       => 4,
                'am_rights'     => 2,
            ],
            [
                'am_id'         => 12,
                'mod_id'        => 6,
                'role_id'       => 4,
                'am_rights'     => 2,
            ],
        ]);
    }
}
