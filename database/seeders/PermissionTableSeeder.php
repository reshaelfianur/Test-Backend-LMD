<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('permissions')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('permissions')->insert([
            [
                'id'            => 1,
                'name'          => 'user-create',
                'display_name'  => 'User Create',
                'description'   => 'Create new User',
                'type'          => 1,
                'submod_id'     => 1,
            ],
            [
                'id'            => 2,
                'name'          => 'user-read',
                'display_name'  => 'User Read',
                'description'   => 'Read User data',
                'type'          => 2,
                'submod_id'     => 1,
            ],
            [
                'id'            => 3,
                'name'          => 'user-not-allowed',
                'display_name'  => 'User Not Allowed',
                'description'   => 'Can not allow access User',
                'type'          => 3,
                'submod_id'     => 1,
            ],
            [
                'id'            => 4,
                'name'          => 'task-create',
                'display_name'  => 'Task Create',
                'description'   => 'Create new Task',
                'type'          => 1,
                'submod_id'     => 2,
            ],
            [
                'id'            => 5,
                'name'          => 'task-read',
                'display_name'  => 'Task Read',
                'description'   => 'Read Task data',
                'type'          => 2,
                'submod_id'     => 2,
            ],
            [
                'id'            => 6,
                'name'          => 'task-not-allowed',
                'display_name'  => 'Task Not Allowed',
                'description'   => 'Can not allow access Task',
                'type'          => 3,
                'submod_id'     => 2,
            ],
        ]);
    }
}
