<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MediaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'mediable_type' => null,
            'mediable_id' => null,
            'type' => fake()->randomElement(['picture', 'file', 'video', 'audio']),
            'url' => fake()->url(),
            'path' => 'storage/media/' . fake()->uuid() . '.jpg',
            'extra' => [
                'size' => fake()->numberBetween(100000, 5000000),
                'mime_type' => 'image/jpeg',
                'dimensions' => [
                    'width' => 1920,
                    'height' => 1080
                ]
            ],
        ];
    }
}
