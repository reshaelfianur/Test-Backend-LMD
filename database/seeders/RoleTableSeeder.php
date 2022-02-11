<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('roles')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \DB::table('roles')->insert([
            [
                'id'            => 1,
                'name'          => 'axia-admin',
                'display_name'  => 'Axia Admin',
            ],
            [
                'id'            => 2,
                'name'          => 'super-admin',
                'display_name'  => 'Super Admin',
            ],
            [
                'id'            => 3,
                'name'          => 'super-user',
                'display_name'  => 'Super User',
            ],
            [
                'id'            => 4,
                'name'          => 'user',
                'display_name'  => 'User',
            ],
        ]);
    }
}
