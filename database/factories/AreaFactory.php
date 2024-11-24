<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Traits\GeneratesSlug;

class AreaFactory extends Factory
{
    use GeneratesSlug;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        return [
            'name' => $name,
            'slug' => $this->generateUniqueSlug($name),
            'description' => fake()->paragraph(),
            'user_id' => User::factory(),
            'parent_id' => null,
            'featured' => fake()->boolean(20),
            'status' => fake()->randomElement(['draft', 'published', 'suspended']),
        ];
    }
}
