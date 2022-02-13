<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubModuleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('sub_modules')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('sub_modules')->insert([
            [
                'submod_id'     => 1,
                'mod_id'        => 1,
                'submod_code'   => 'user',
                'submod_name'   => 'User',
            ],
            [
                'submod_id'     => 2,
                'mod_id'        => 2,
                'submod_code'   => 'task',
                'submod_name'   => 'Task',
            ],
        ]);
    }
}
