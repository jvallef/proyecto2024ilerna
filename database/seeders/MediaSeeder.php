<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Content;
use App\Models\Path;
use App\Models\Area;
use App\Models\Message;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MediaSeeder extends Seeder
{
    /**
     * Probabilidad de que un modelo tenga una imagen (10%)
     */
    const IMAGE_PROBABILITY = 10;

    /**
     * Crea una imagen temporal para el seeding
     */
    private function createTemporaryImage(string $prefix, int $minSize = 100, int $maxSize = 200): string
    {
        $width = rand($minSize, $maxSize);
        $height = rand($minSize, $maxSize);
        $image = imagecreatetruecolor($width, $height);
        
        // Color de fondo aleatorio
        $bgColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
        imagefill($image, 0, 0, $bgColor);
        
        // Si es un avatar, añadir un círculo
        if ($prefix === 'avatar') {
            $circleColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
            imagefilledellipse($image, $width/2, $height/2, $width*0.8, $height*0.8, $circleColor);
        }
        
        // Guardar la imagen temporal
        $tempFile = storage_path('app/public/temp/' . $prefix . '_' . uniqid() . '.jpg');
        imagejpeg($image, $tempFile, 80); // Calidad 80 para archivos más pequeños
        imagedestroy($image);

        return $tempFile;
    }

    public function run(): void
    {
        // Asegurarnos de que existe el directorio temporal
        if (!Storage::disk('public')->exists('temp')) {
            Storage::disk('public')->makeDirectory('temp');
        }

        // Usuarios (avatares)
        foreach (User::all() as $user) {
            if (rand(1, 100) <= self::IMAGE_PROBABILITY) {
                $tempFile = $this->createTemporaryImage('avatar');
                $user->addMedia($tempFile)
                     ->preservingOriginal()
                     ->toMediaCollection('avatar');
                @unlink($tempFile);
            }
        }

        // Cursos
        foreach (Course::all() as $course) {
            if (rand(1, 100) <= self::IMAGE_PROBABILITY) {
                $tempFile = $this->createTemporaryImage('course');
                $course->addMedia($tempFile)
                       ->preservingOriginal()
                       ->toMediaCollection('images');
                @unlink($tempFile);
            }
        }

        // Contenidos
        foreach (Content::all() as $content) {
            if (rand(1, 100) <= self::IMAGE_PROBABILITY) {
                $tempFile = $this->createTemporaryImage('content');
                $content->addMedia($tempFile)
                        ->preservingOriginal()
                        ->toMediaCollection('images');
                @unlink($tempFile);
            }
        }

        // Paths
        foreach (Path::all() as $path) {
            if (rand(1, 100) <= self::IMAGE_PROBABILITY) {
                $tempFile = $this->createTemporaryImage('path');
                $path->addMedia($tempFile)
                     ->preservingOriginal()
                     ->toMediaCollection('images');
                @unlink($tempFile);
            }
        }

        // Areas
        foreach (Area::all() as $area) {
            if (rand(1, 100) <= self::IMAGE_PROBABILITY) {
                $tempFile = $this->createTemporaryImage('area');
                $area->addMedia($tempFile)
                     ->preservingOriginal()
                     ->toMediaCollection('images');
                @unlink($tempFile);
            }
        }

        // Messages
        foreach (Message::all() as $message) {
            if (rand(1, 100) <= self::IMAGE_PROBABILITY) {
                $tempFile = $this->createTemporaryImage('message');
                $message->addMedia($tempFile)
                        ->preservingOriginal()
                        ->toMediaCollection('images');
                @unlink($tempFile);
            }
        }

        // Limpiar el directorio temporal
        Storage::disk('public')->deleteDirectory('temp');
    }
}
