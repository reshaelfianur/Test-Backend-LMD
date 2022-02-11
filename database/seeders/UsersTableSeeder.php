<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('users')->truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        \DB::table('users')->insert(
            array(
                0 =>
                array(
                    'user_id'                       => 1,
                    'comp_id'                       => 1,
                    'user_fullname'                 => 'Axia Solusi',
                    'user_email'                    => 'help.desk@axiasolusi.co.id',
                    'username'                      => 'axia',
                    'password'                      => Hash::make('axia1234'),
                    'user_need_change_password'     => 2,
                    'user_type'                     => 1,
                ),
            )
        );
    }
}
