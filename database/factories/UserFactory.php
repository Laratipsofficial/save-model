<?php

namespace Asdh\SaveModel\Database\Factories;

use Asdh\SaveModel\Tests\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
  protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'image' => null,
            'remember_token' => \Illuminate\Support\Str::random(10),
        ];
    }
}