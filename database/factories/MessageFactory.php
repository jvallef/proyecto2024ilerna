<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Area;
use App\Models\Path;
use App\Models\Course;
use App\Models\Content;
use Illuminate\Database\Eloquent\Factories\Factory;

class MessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(),
            'body' => fake()->paragraph(),
            'user_id' => User::factory(),
            'private' => fake()->boolean(20),
            'user_to_id' => null,
            'area_id' => null,
            'path_id' => null,
            'course_id' => null,
            'content_id' => null,
        ];
    }
}
