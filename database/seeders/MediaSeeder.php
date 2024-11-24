<?php

namespace Database\Seeders;

use App\Models\Media;
use App\Models\Course;
use App\Models\Content;
use App\Models\Path;
use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Seeder;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        $models = [
            Course::class => Course::all(),
            Content::class => Content::all(),
            Path::class => Path::all(),
            Area::class => Area::all(),
        ];

        foreach ($models as $modelClass => $items) {
            foreach ($items as $item) {
                $mediaCount = rand(1, 3);   //1 a 3 medias por cada item
                for ($i = 0; $i < $mediaCount; $i++) {
                    Media::factory()->create([
                        'mediable_type' => $modelClass,
                        'mediable_id' => $item->id,
                        'user_id' => User::role('teacher')->inRandomOrder()->first()->id,
                    ]);
                }
            }
        }
    }
}

