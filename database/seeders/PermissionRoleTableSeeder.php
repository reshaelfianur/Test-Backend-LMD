<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('permission_role')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \DB::table('permission_role')->insert([
            [
                'permission_id'     => 1,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 4,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 7,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 10,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 13,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 16,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 19,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 22,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 25,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 28,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 31,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 34,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 37,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 40,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 43,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 46,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 49,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 52,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 55,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 58,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 61,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 63,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 66,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 68,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 70,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 72,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 74,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 76,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 78,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 80,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 82,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 84,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 86,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 88,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 90,
                'role_id'           => 3,
            ],
            [
                'permission_id'     => 93,
                'role_id'           => 3,
            ],
        ]);
    }
}
