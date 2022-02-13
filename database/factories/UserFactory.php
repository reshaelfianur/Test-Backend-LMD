<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $name       = $this->faker->name;
        $custName   = str_replace(' ', '_', strtolower($name));

        return [
            'email'             => $this->faker->unique()->safeEmail,
            'username'          => $custName,
            'password'          => Hash::make($custName),
            'user_full_name'    => $name,
            'user_type'         => 2,
            'user_active_date'  => Carbon::now()->format('Y-m-d'),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                // 'email_verified_at' => null,
            ];
        });
    }
}
