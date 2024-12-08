<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    private $programmingTopics = [
        'Introducción a la Programación' => [
            'Conceptos Básicos de Programación',
            'Algoritmos y Pseudocódigo',
            'Estructuras de Control',
            'Tipos de Datos Fundamentales'
        ],
        'Desarrollo Web' => [
            'HTML5 y CSS3 Básico',
            'JavaScript Fundamentals',
            'Responsive Design',
            'APIs y AJAX'
        ],
        'Bases de Datos' => [
            'Modelado de Datos',
            'SQL Básico',
            'Normalización',
            'Gestión de Índices'
        ],
        'Seguridad Informática' => [
            'Principios de Ciberseguridad',
            'Criptografía Básica',
            'Seguridad en Redes',
            'Ethical Hacking'
        ]
    ];

    private function generateContent($topic, $subtopic)
    {
        return [
            'markdown' => "# $subtopic\n\n" .
                "## Introducción\n\n" .
                "En esta lección aprenderemos sobre $subtopic, un componente esencial de $topic.\n\n" .
                "## Conceptos Clave\n\n" .
                "1. Fundamentos básicos\n" .
                "2. Mejores prácticas\n" .
                "3. Aplicaciones prácticas\n\n" .
                "## Ejemplos Prácticos\n\n" .
                "```php\n" .
                "// Ejemplo de código\n" .
                "function ejemplo() {\n" .
                "    echo 'Aprendiendo $subtopic';\n" .
                "}\n" .
                "```\n\n" .
                "## Ejercicios\n\n" .
                "1. Implementa los conceptos básicos vistos\n" .
                "2. Desarrolla un proyecto simple usando lo aprendido\n" .
                "3. Analiza y mejora el código proporcionado\n\n" .
                "## Recursos Adicionales\n\n" .
                "- Documentación oficial\n" .
                "- Tutoriales recomendados\n" .
                "- Proyectos de ejemplo"
        ];
    }

    public function run(): void
    {
        $courses = Course::all();
        $teachers = User::role('teacher')->get();

        foreach ($courses as $course) {
            // Crear contenidos principales para cada curso
            foreach ($this->programmingTopics as $topic => $subtopics) {
                $mainContent = Content::create([
                    'title' => $topic,
                    'type' => 'theme',
                    'status' => 'published',
                    'author_id' => $teachers->random()->id,
                    'parent_id' => null,
                    'content' => $this->generateContent($topic, $topic)
                ]);

                $course->contents()->attach($mainContent->id, [
                    'sort' => rand(2, 10)
                ]);

                // Crear sub-contenidos
                foreach ($subtopics as $index => $subtopic) {
                    $subContent = Content::create([
                        'title' => $subtopic,
                        'type' => 'lesson',
                        'status' => 'published',
                        'parent_id' => $mainContent->id,
                        'author_id' => $teachers->random()->id,
                        'content' => $this->generateContent($topic, $subtopic)
                    ]);

                    // Crear algunos quizzes
                    if ($index % 2 == 0) {
                        Content::create([
                            'title' => "Quiz: $subtopic",
                            'type' => 'quizz',
                            'status' => 'published',
                            'parent_id' => $subContent->id,
                            'author_id' => $teachers->random()->id,
                            'content' => [
                                'markdown' => "# Quiz de $subtopic\n\n" .
                                    "Evalúa tu comprensión de $subtopic con las siguientes preguntas:\n\n" .
                                    "1. ¿Cuál es el concepto principal de $subtopic?\n" .
                                    "2. Explica las mejores prácticas en $subtopic\n" .
                                    "3. Desarrolla un ejemplo práctico de $subtopic"
                            ]
                        ]);
                    }
                }
            }
        }
    }
}
