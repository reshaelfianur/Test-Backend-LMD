<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('modules')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('modules')->insert([
            [
                'mod_id'        => 1,
                'mod_code'      => 'user-management',
                'mod_name'      => 'User Management',
                'mod_status'    => 1,
            ],
            [
                'mod_id'        => 2,
                'mod_code'      => 'task-management',
                'mod_name'      => 'Task Management',
                'mod_status'    => 1,
            ],
        ]);
    }
}
