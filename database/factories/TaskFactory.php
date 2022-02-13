<?php

namespace Database\Factories;

use App\Models\Task;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\DB;

class TaskFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $userId = \App\Models\User::factory()->create()->user_id;

        DB::table('role_user')->insert([
            [
                'role_id'   => 2,
                'user_id'   => $userId,
                'user_type' => 'App\Models\User',
            ],
        ]);

        return [
            'user_id'                   => $userId,
            'task_title'                => $this->faker->title,
            'task_description'          => $this->faker->text,
            'task_hours'                => $this->faker->randomNumber(2),
            'task_notes'                => $this->faker->text,
            'created_by'                => $userId,
        ];
    }
}
