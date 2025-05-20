<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Story>
 */
class StoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "title" => fake()->sentence(),
            "synopsis" => fake()->paragraph(),
            "genre" => "humor",
            "content" => fake()->paragraphs(rand(3, 10), true),
            "user_id" => \App\Models\User::factory(),
        ];
    }
}
