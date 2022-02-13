<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use Illuminate\Support\Facades\DB;

class TaskTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('tasks')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('tasks')->insert(
            [
                [
                    'user_id'                   => 1,
                    'task_title'                => $faker->title,
                    'task_description'          => $faker->text,
                    'task_hours'                => $faker->randomNumber(2),
                    'task_notes'                => $faker->text,
                    'created_by'                => 1,
                ],
                [
                    'user_id'                   => 2,
                    'task_title'                => $faker->title,
                    'task_description'          => $faker->text,
                    'task_hours'                => $faker->randomNumber(2),
                    'task_notes'                => $faker->text,
                    'created_by'                => 2,
                ],
            ]
        );

        Task::factory()
            ->count(5)
            ->create();
    }
}
