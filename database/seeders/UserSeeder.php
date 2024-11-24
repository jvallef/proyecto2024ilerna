<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear usuarios predefinidos y asignar roles
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole(['admin', 'teacher', 'student']);

        $teacher1 = User::create([
            'name' => 'Profe 1',
            'email' => 'profe1@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        $teacher1->assignRole('teacher');

        $teacherStudent = User::create([
            'name' => 'Profe Estudiante',
            'email' => 'profe-estudiante@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        $teacherStudent->assignRole(['teacher', 'student']);

        $teacherStudent2 = User::create([
            'name' => 'Profe Estudiante 2',
            'email' => 'profe-estudiante2@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        $teacherStudent2->assignRole(['teacher', 'student']);

        $student1 = User::create([
            'name' => 'Estudiante 1',
            'email' => 'estudiante1@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        $student1->assignRole('student');

        $student2 = User::create([
            'name' => 'Estudiante 2',
            'email' => 'estudiante2@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        $student2->assignRole('student');

        // Usuarios aleatorios generados con factories
        User::factory()->count(27)->create()->each(function ($user) {
            $user->assignRole('student');
        });

        User::factory()->count(9)->create()->each(function ($user) {
            $user->assignRole('teacher');
        });
    }
}
