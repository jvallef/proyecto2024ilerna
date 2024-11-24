<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Traits\GeneratesSlug;

class CourseFactory extends Factory
{
    use GeneratesSlug;
    public function definition(): array
    {
        $title = fake()->unique()->sentence();
        return [
            'title' => $title,
            'slug' => $this->generateUniqueSlug($title),
            'description' => fake()->paragraph(),
            'author_id' => User::factory(),
            'featured' => fake()->boolean(20),
            'age_group' => fake()->randomElement(['0-6', '7-12', '13-20', '21+', null]),
            'status' => fake()->randomElement(['draft', 'published', 'suspended']),
        ];
    }
}
