<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('modules')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \DB::table('modules')->insert([
            [
                'mod_id'        => 1,
                'mod_code'      => 'entity',
                'mod_name'      => 'Entity',
                'mod_status'    => 1,
            ],
            [
                'mod_id'        => 2,
                'mod_code'      => 'payroll-setting',
                'mod_name'      => 'Payroll Setting',
                'mod_status'    => 1,
            ],
            [
                'mod_id'        => 3,
                'mod_code'      => 'personnel',
                'mod_name'      => 'Personnel',
                'mod_status'    => 1,
            ],
            [
                'mod_id'        => 4,
                'mod_code'      => 'payroll',
                'mod_name'      => 'Payroll',
                'mod_status'    => 1,
            ],
            [
                'mod_id'        => 5,
                'mod_code'      => 'report',
                'mod_name'      => 'Report',
                'mod_status'    => 1,
            ],
            [
                'mod_id'        => 6,
                'mod_code'      => 'user-management',
                'mod_name'      => 'User Management',
                'mod_status'    => 1,
            ],
        ]);
    }
}
