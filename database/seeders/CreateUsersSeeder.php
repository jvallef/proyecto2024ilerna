<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB; // para resetear la tabla Users

class CreateUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          // Opcional: Resetear tabla users
          // ¡¡¡ Elimina las claves foráneas y TODOS los usuarios existentes !!!

          // DB::statement('SET FOREIGN_KEY_CHECKS=0');
          // User::truncate();  // Elimina todos los usuarios existentes. ¡Ten mucho cuidado!
          // DB::statement('SET FOREIGN_KEY_CHECKS=1');


        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'admin, teacher, student',
        ]);

        User::create([
            'name' => 'Profe 1',
            'email' => 'profe1@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'teacher',
        ]);

        User::create([
            'name' => 'Profe 2',
            'email' => 'profe2@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'teacher, student',
        ]);

        User::create([
            'name' => 'Estudiante 1',
            'email' => 'estudiante1@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);

        User::create([
            'name' => 'Estudiante 2',
            'email' => 'estudiante2@example.com',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
            'role' => 'student',
        ]);
    }
}
