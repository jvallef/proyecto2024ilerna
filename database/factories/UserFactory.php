<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->name();
        // Convertir el nombre a slug, reemplazar guiones por puntos y eliminar puntos duplicados
        $baseEmail = Str::slug($name, '.');
        $baseEmail = preg_replace('/\.+/', '.', $baseEmail);
        $baseEmail = trim($baseEmail, '.');
        
        // Generate a unique email by adding a number suffix if needed
        $email = $baseEmail . '@example.com';
        $counter = 1;
        while (static::emailExists($email)) {
            $email = $baseEmail . $counter . '@example.com';
            $counter++;
        }

        return [
            'name' => $name,
            'email' => $email,
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Check if an email already exists
     */
    protected static function emailExists(string $email): bool
    {
        return \App\Models\User::where('email', $email)->exists();
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
