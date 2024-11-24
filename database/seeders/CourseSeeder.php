<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Path;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Traits\GeneratesSlug;


class CourseSeeder extends Seeder
{
    use GeneratesSlug;
    public function run(): void
    {
        $teachers = User::role('teacher')->get();

        if (!$teachers) {
            throw new \Exception("No se encontró un usuario con el rol de 'admin'. Asegúrate de que el UserSeeder se ejecutó correctamente.");
        }


        // Definir algunos cursos específicos con sus paths correspondientes
        $coursesData = [
            // Cursos de Programación
            [
                'title' => 'Fundamentos de Python',
                'description' => 'Aprende los conceptos básicos de Python: variables, tipos de datos, estructuras de control y funciones.',
                'paths' => ['Programación - Nivel Básico'],
                'sort' => 1
            ],
            [
                'title' => 'Python Intermedio: POO y Estructuras de Datos',
                'description' => 'Profundiza en Python con programación orientada a objetos y estructuras de datos avanzadas.',
                'paths' => ['Programación - Nivel Intermedio'],
                'sort' => 2
            ],
            [
                'title' => 'Python Avanzado: Patrones de Diseño y Mejores Prácticas',
                'description' => 'Domina Python con patrones de diseño, testing y optimización de código.',
                'paths' => ['Programación - Nivel Avanzado'],
                'sort' => 3
            ],

            // Curso que pertenece a múltiples paths
            [
                'title' => 'Matemáticas para Programación y IA',
                'description' => 'Fundamentos matemáticos esenciales para programación y machine learning.',
                'paths' => [
                    'Programación - Nivel Intermedio',
                    'Inteligencia artificial - Nivel Básico',
                    'Álgebra - Nivel Básico'
                ],
                'sort' => 1
            ],

            // Cursos de Marketing Digital
            [
                'title' => 'Introducción al Marketing Digital',
                'description' => 'Conceptos fundamentales del marketing digital y estrategias básicas.',
                'paths' => ['Marketing digital - Nivel Básico'],
                'sort' => 1
            ],
            [
                'title' => 'SEO y Posicionamiento Web',
                'description' => 'Aprende a optimizar sitios web para motores de búsqueda.',
                'paths' => ['Marketing digital - Nivel Intermedio'],
                'sort' => 2
            ],

            // Cursos de Desarrollo Web
            [
                'title' => 'HTML5 y CSS3 Fundamentals',
                'description' => 'Aprende a crear páginas web con HTML5 y CSS3.',
                'paths' => ['Desarrollo web - Nivel Básico'],
                'sort' => 1
            ],
            [
                'title' => 'JavaScript Moderno',
                'description' => 'Desarrollo web con JavaScript moderno y ES6+.',
                'paths' => [
                    'Desarrollo web - Nivel Intermedio',
                    'Programación - Nivel Intermedio'
                ],
                'sort' => 2
            ],

            // Algunos cursos aleatorios pero coherentes
            [
                'title' => 'Introducción a la Fotografía Digital',
                'description' => 'Fundamentos de la fotografía digital y manejo de cámara.',
                'paths' => ['Fotografía - Nivel Básico'],
                'sort' => 1
            ],
            [
                'title' => 'Mindfulness y Meditación',
                'description' => 'Técnicas básicas de mindfulness y meditación para principiantes.',
                'paths' => ['Mindfulness - Nivel Básico', 'Meditación - Nivel Básico'],
                'sort' => 1
            ]
        ];

        // Crear los cursos definidos
        foreach ($coursesData as $courseData) {
            $teacher = $teachers->random();
            $course = Course::create([
                'title' => $courseData['title'],
                'slug' => $this->generateUniqueSlug($courseData['title']),
                'description' => $courseData['description'],
                'author_id' => $teacher->id,
                'featured' => false,
                'status' => 'published'
            ]);

            // Asociar con los paths correspondientes
            foreach ($courseData['paths'] as $pathName) {
                $path = Path::where('name', 'like', "%$pathName%")->first();
                if ($path) {
                    $course->paths()->attach($path->id, [
                        'sort' => $courseData['sort']
                    ]);
                }
            }
        }

        // Crear algunos cursos adicionales aleatorios para otros paths
        foreach ($teachers as $teacher) {
            $courses = Course::factory()->count(2)->create([
                'author_id' => $teacher->id,
                'status' => 'published'
            ]);

            foreach ($courses as $course) {
                $randomPaths = Path::inRandomOrder()->limit(2)->get();
                foreach ($randomPaths as $path) {
                    $course->paths()->attach($path->id, [
                        'sort' => rand(1, 10)
                    ]);
                }
            }
        }
    }
}
