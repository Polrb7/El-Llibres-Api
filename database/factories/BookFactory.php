<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => 'Yow',
            'author' => fake()->name(),
            'genre' => fake()->word(),
            'description' => fake()->sentence(),
            'book_img' => fake()->imageUrl(640, 480, 'book', true),
        ];
    }
}
