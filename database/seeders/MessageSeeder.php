<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\User;
use App\Models\Course;
use App\Models\Path;
use App\Models\Area;
use Illuminate\Database\Seeder;

class MessageSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $courses = Course::all();
        $paths = Path::all();
        $areas = Area::all();

        // Crear mensajes públicos
        foreach ($areas as $area) {
            Message::factory()->count(2)->create([
                'user_id' => $users->random()->id,
                'area_id' => $area->id,
                'private' => false
            ]);
        }

        foreach ($paths as $path) {
            Message::factory()->count(2)->create([
                'user_id' => $users->random()->id,
                'path_id' => $path->id,
                'private' => false
            ]);
        }

        foreach ($courses as $course) {
            Message::factory()->count(2)->create([
                'user_id' => $users->random()->id,
                'course_id' => $course->id,
                'private' => false
            ]);
        }

        // Crear algunos mensajes privados
        for ($i = 0; $i < 10; $i++) {
            $sender = $users->random();
            $receiver = $users->except($sender->id)->random();

            Message::factory()->create([
                'user_id' => $sender->id,
                'user_to_id' => $receiver->id,
                'private' => true
            ]);
        }
    }
}
