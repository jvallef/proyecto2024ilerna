<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Path;
use Illuminate\Database\Seeder;

class EnrollmentSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::where('role', 'like', '%student%')->get();
        $courses = Course::all();
        $paths = Path::all();

        // Inscribir estudiantes en cursos
        foreach ($students as $student) {
            // Inscribir en 1-5 cursos aleatorios
            $randomCourses = $courses->random(rand(1, 5));
            foreach ($randomCourses as $course) {
                $student->enrolledCourses()->attach($course->id, [
                    'role' => 'student',
                    'progress' => rand(0, 100),
                    'completed' => rand(0, 1)
                ]);
            }

            // Inscribir en 1-3 paths aleatorios
            $randomPaths = $paths->random(rand(1, 3));
            foreach ($randomPaths as $path) {
                $student->enrolledPaths()->attach($path->id, [
                    'role' => 'student',
                    'progress' => rand(0, 100),
                    'completed' => rand(0, 1)
                ]);
            }
        }

        // Inscribir profesores en sus propios cursos como profesores
        $teachers = User::where('role', 'like', '%teacher%')->get();
        foreach ($teachers as $teacher) {
            $teacherCourses = Course::where('author_id', $teacher->id)->get();
            foreach ($teacherCourses as $course) {
                $teacher->enrolledCourses()->attach($course->id, [
                    'role' => 'teacher',
                    'progress' => 100,
                    'completed' => true
                ]);
            }
        }
    }
}
