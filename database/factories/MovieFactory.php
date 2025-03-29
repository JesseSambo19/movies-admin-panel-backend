<?php

namespace Database\Factories;

use App\Models\Movie;
use Illuminate\Database\Eloquent\Factories\Factory;

class MovieFactory extends Factory
{
    protected $model = Movie::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'openingText' => $this->faker->paragraph(),
            'releaseDate' => $this->faker->date(),
            'user_id' => 1, // Ensure a valid user exists
        ];
    }
}
