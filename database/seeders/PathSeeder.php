<?php

namespace Database\Seeders;

use App\Models\Path;
use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Traits\GeneratesSlug;


class PathSeeder extends Seeder
{
    use GeneratesSlug;

    public function run(): void
    {
        $admin = User::role('admin')->first();

        if (!$admin) {
            throw new \Exception("No se encontró un usuario con el rol de 'admin'. Asegúrate de que el UserSeeder se ejecutó correctamente.");
        }

        $pathsPorPlaza = [
            // Barrio de la Cultura
            'Plaza del Arte' => [
                'Pintura y Dibujo',
                'Música',
                'Fotografía',
                'Historia del Arte'
            ],
            'Plaza de las Lenguas' => [
                'Inglés',
                'Español',
                'Literatura castellana',
                'Literatura universal',
                'Escritura creativa'
            ],
            'Plaza de la Filosofía' => [
                'Filosofía clásica',
                'Filosofía del Siglo XIX',
                'Grandes filósofos de Grecia y Roma',
                'Ética',
                'Lógica',
                'Pensamiento crítico'
            ],
            'Plaza de la Cultura' => [
                'Antropología cultural',
                'Estudios interculturales',
                'Tradiciones del mundo'
            ],
            'Plaza de la Historia' => [
                'Historia antigua',
                'Historia medieval',
                'Historia moderna',
                'Historia contemporánea',
                'Historia del Imperio Romano',
                'El Descubrimiento de América',
                'Arqueología'
            ],

            // Barrio de la Ciencia
            'Plaza de la Ciencia' => [
                'Biología',
                'Química',
                'Astronomía',
                'Geología',
                'El método científico'
            ],
            'Plaza de la Tecnología' => [
                'Electrónica',
                'Mecánina hidraúlica',
                'Programación',
                'Desarrollo web',
                'Inteligencia artificial',
                'Ciberseguridad',
                'Robótica'
            ],
            'Plaza de las Matemáticas' => [
                'Álgebra',
                'Cálculo',
                'Geometría',
                'Estadística',
                'Probabilidad'
            ],
            'Plaza de la Física' => [
                'Mecánica clásica',
                'Termodinámica',
                'Electromagnetismo',
                'Física cuántica',
                'Relatividad'
            ],

            // Barrio de la Sociedad
            'Plaza de las Ciencias Sociales' => [
                'Sociología',
                'Psicología social',
                'Antropología',
                'Política',
                'Demografía'
            ],
            'Plaza de la Economía' => [
                'Finanzas personales',
                'Economía global',
                'Inversiones',
                'Criptomonedas',
                'Microeconomía'
            ],
            'Plaza de los Negocios' => [
                'Emprendimiento',
                'Gestión empresarial',
                'Liderazgo',
                'Estrategia de negocio',
                'Innovación empresarial'
            ],
            'Plaza del Marketing' => [
                'Marketing digital',
                'Branding',
                'Marketing de contenidos',
                'Análisis de mercado',
                'Social media marketing'
            ],
            'Plaza de la Comunicación' => [
                'Oratoria',
                'Periodismo',
                'Comunicación digital',
                'Relaciones públicas'
            ],

            // Salud y Bienestar
            'Plaza de la Felicidad' => [
                'Psicología positiva',
                'Mindfulness',
                'Inteligencia emocional',
                'Meditación',
                'Relaciones personales sanas'
            ],
            'Plaza de la Salud' => [
                'Nutrición',
                'Ejercicio físico',
                'Salud mental',
                'Medicina preventiva',
                'Primeros auxilios'
            ],
            'Plaza del Bienestar Personal' => [
                'Gestión del estrés',
                'Hábitos saludables',
                'Equilibrio trabajo-vida',
                'Autocuidado',
                'Desarrollo de Resiliencia',
                'Salud mental en el trabajo',
                'Tiempo de calidad y ocio',
                'Dormir bien'
            ]
        ];

        $sortOrder = 1; // Contador para el orden global

        foreach ($pathsPorPlaza as $plazaNombre => $paths) {
            $plaza = Area::where('name', $plazaNombre)->first();
            if ($plaza) {
                foreach ($paths as $pathName) {

                    $slug = $this->generateUniqueSlug($pathName);

                    // Meta básico para SEO
                    $meta = json_encode([
                        'seo' => [
                            'title' => $pathName,
                            'description' => "Ruta de aprendizaje de {$pathName} en {$plazaNombre}",
                            'keywords' => [strtolower($pathName), 'educación', 'aprendizaje', 'ruta']
                        ]
                    ]);

                    $mainPath = Path::create([
                        'name' => $pathName,
                        'slug' => $slug,
                        'area_id' => $plaza->id,
                        'user_id' => $admin->id,
                        'parent_id' => null,
                        'sort_order' => $sortOrder++,
                        'meta' => $meta
                    ]);

                    // Crear sub-paths para cada path principal
                    $subPaths = [
                        'Nivel Básico' => 'Fundamentos y conceptos básicos de ' . $pathName,
                        'Nivel Intermedio' => 'Conocimientos intermedios de ' . $pathName,
                        'Nivel Avanzado' => 'Dominio avanzado de ' . $pathName
                    ];

                    foreach ($subPaths as $level => $description) {
                        $subPathName = $pathName . ' - ' . $level;
                        $slug = $this->generateUniqueSlug($subPathName);

                        // Meta para sub-paths
                        $subMeta = json_encode([
                            'seo' => [
                                'title' => $subPathName,
                                'description' => $description,
                                'keywords' => [strtolower($pathName), strtolower($level), 'educación', 'aprendizaje']
                            ]
                        ]);

                        Path::create([
                            'name' => $subPathName,
                            'slug' => $slug,
                            'parent_id' => $mainPath->id,
                            'area_id' => $plaza->id,
                            'user_id' => User::role('admin')->inRandomOrder()->first()->id,
                            'sort_order' => $sortOrder++,
                            'meta' => $subMeta
                        ]);
                    }
                }
            }
        }
    }
}
