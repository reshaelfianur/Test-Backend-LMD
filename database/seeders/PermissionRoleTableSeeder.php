<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permission_role')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('permission_role')->insert([
            [
                'permission_id'     => 1,
                'role_id'           => 1,
            ],
            [
                'permission_id'     => 4,
                'role_id'           => 1,
            ],
            [
                'permission_id'     => 2,
                'role_id'           => 2,
            ],
            [
                'permission_id'     => 4,
                'role_id'           => 2,
            ],
        ]);
    }
}
