<?php

namespace Database\Seeders;

use App\Models\Content;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();
        $teachers = User::role('teacher')->get();

        foreach ($courses as $course) {
            // Crear contenidos principales para cada curso
            $mainContents = Content::factory()->count(3)->create([
                'author_id' => $teachers->random()->id,
                'parent_id' => null
            ]);

            // Asociar contenidos al curso
            foreach ($mainContents as $content) {
                $course->contents()->attach($content->id, [
                    'sort' => rand(1, 10)
                ]);

                // Crear sub-contenidos
                Content::factory()->count(2)->create([
                    'parent_id' => $content->id,
                    'author_id' => $teachers->random()->id
                ]);
            }
        }
    }
}
