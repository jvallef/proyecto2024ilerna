<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Traits\GeneratesSlug;

class ContentFactory extends Factory
{
    use GeneratesSlug;

    public function definition(): array
    {
        $title = fake()->unique()->sentence();
        $basicContent = [
            'content' => fake()->paragraphs(3, true),
            'sections' => [
                [
                    'title' => 'Sección 1',
                    'description' => fake()->sentence(),
                    'content' => fake()->paragraphs(2, true),
                    'type' => 'text'
                ],
                [
                    'title' => 'Sección 2',
                    'description' => fake()->sentence(),
                    'content' => fake()->paragraphs(2, true),
                    'type' => 'text'
                ]
            ]
        ];

        return [
            'type' => fake()->randomElement(['content', 'book', 'theme', 'lesson', 'quizz']),
            'title' => $title,
            'slug' => $this->generateUniqueSlug($title),
            'content' => $basicContent,
            'author_id' => User::factory(),
            'parent_id' => null,
        ];
    }
}
