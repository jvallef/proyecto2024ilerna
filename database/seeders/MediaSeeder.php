<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class MediaSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurarnos de que existe el directorio temporal
        if (!Storage::disk('public')->exists('temp')) {
            Storage::disk('public')->makeDirectory('temp');
        }

        // Obtener todos los usuarios
        $users = User::all();

        foreach ($users as $user) {
            // 70% de probabilidad de tener avatar
            if (rand(1, 100) <= 70) {
                // Crear un avatar temporal para el seed
                $size = rand(200, 400);
                $image = imagecreatetruecolor($size, $size);
                
                // Crear un color aleatorio para el fondo
                $bgColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
                imagefill($image, 0, 0, $bgColor);
                
                // Añadir un círculo en el centro como avatar
                $circleColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
                imagefilledellipse($image, $size/2, $size/2, $size*0.8, $size*0.8, $circleColor);
                
                // Guardar la imagen temporal
                $tempFile = storage_path('app/public/temp/avatar_' . uniqid() . '.jpg');
                imagejpeg($image, $tempFile, 90);
                imagedestroy($image);

                // Añadir el avatar al usuario
                $user->addMedia($tempFile)
                     ->preservingOriginal()
                     ->toMediaCollection('avatar');

                // Limpiar el archivo temporal
                @unlink($tempFile);
            }
        }

        // Limpiar el directorio temporal
        Storage::disk('public')->deleteDirectory('temp');
    }
}
