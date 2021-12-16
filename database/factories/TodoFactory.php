<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $status = ["undone", "completed"];

        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->text(),
            'status' => $status[rand(0, 1)],
            'expiration' => $this->faker->date(),
        ];
    }
}
