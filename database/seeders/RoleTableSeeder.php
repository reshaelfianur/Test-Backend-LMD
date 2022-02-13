<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('roles')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('roles')->insert([
            [
                'id'            => 1,
                'name'          => 'admin',
                'display_name'  => 'Admin',
            ],
            [
                'id'            => 2,
                'name'          => 'user',
                'display_name'  => 'User',
            ],
        ]);
    }
}
