<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Traits\GeneratesSlug;

class AreaSeeder extends Seeder
{
    use GeneratesSlug;

    public function run(): void
    {
        $admin = User::role('admin')->first();

        if (!$admin) {
            throw new \Exception("No se encontró un usuario con el rol de 'admin'. Asegúrate de que el UserSeeder se ejecutó correctamente.");
        }

        // Los 4 distritos principales con sus lugares
        $distritos = [
            'Barrio de la Cultura' => [
                'Plaza del Arte',
                'Plaza de las Lenguas',
                'Plaza de la Filosofía',
                'Plaza de la Cultura',
                'Plaza de la Historia'
            ],
            'Barrio de la Ciencia' => [
                'Plaza de la Ciencia',
                'Plaza de la Tecnología',
                'Plaza de las Matemáticas',
                'Plaza de la Física'
            ],
            'Barrio de la Sociedad' => [
                'Plaza de las Ciencias Sociales',
                'Plaza de la Economía',
                'Plaza de los Negocios',
                'Plaza del Marketing',
                'Plaza de la Comunicación'
            ],
            'Salud y Bienestar' => [
                'Plaza de la Felicidad',
                'Plaza de la Salud',
                'Plaza del Bienestar Personal'
            ]
        ];

        foreach ($distritos as $distrito => $plazas) {

            $slug = $this->generateUniqueSlug($distrito);

            $mainArea = Area::create([
                'name' => $distrito,
                'slug' => $slug,
                'user_id' => $admin->id,
                'parent_id' => null
            ]);

            foreach ($plazas as $plaza) {

                $slug = $this->generateUniqueSlug($distrito);

                Area::create([
                    'name' => $plaza,
                    'slug' => $slug,
                    'parent_id' => $mainArea->id,
                    'user_id' => User::role('admin')->inRandomOrder()->first()->id,
                ]);
            }
        }
    }
}
