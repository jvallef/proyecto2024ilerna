<?php

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;

class ImageController extends FileController
{
    protected $manager;
    protected $thumbnailSize = 150;
    protected $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    protected $maxSize = 5120; // 5MB in kilobytes

    public function __construct()
    {
        $this->directory = 'images'; // Sobreescribir el directorio del padre
        $this->manager = new ImageManager(new Driver());
    }

    public function store(Request $request)
    {
        try {
            Log::info('Iniciando subida de imagen', [
                'request_all' => $request->all(),
                'files' => $request->allFiles()
            ]);

            // Validar el archivo
            if (!$request->hasFile('file')) {
                Log::error('No se encontró archivo en la solicitud');
                return response()->json(['error' => 'No file uploaded'], 400);
            }

            $file = $request->file('file');

            Log::info('Archivo recibido', [
                'mime_type' => $file->getMimeType(),
                'size' => $file->getSize(),
                'original_name' => $file->getClientOriginalName()
            ]);

            // Validar tipo y tamaño
            if (!in_array($file->getMimeType(), $this->allowedTypes)) {
                Log::error('Tipo de archivo no válido', ['mime_type' => $file->getMimeType()]);
                return response()->json(['error' => 'Invalid file type'], 400);
            }

            if ($file->getSize() > $this->maxSize * 1024) {
                Log::error('Archivo demasiado grande', ['size' => $file->getSize()]);
                return response()->json(['error' => 'File too large'], 400);
            }

            // Guardar el archivo y crear el registro Media
            $response = parent::store($request);
            
            if ($response->status() !== 200) {
                return $response;
            }

            // Si el guardado fue exitoso, crear thumbnail
            try {
                $data = json_decode($response->getContent(), true);
                $media = Media::find($data['media']['id']);
                
                if (!$media) {
                    return response()->json(['error' => 'Error processing image'], 500);
                }

                $thumbnailPath = $this->createThumbnail($file, $media->name);
                
                // Actualizar el registro Media con la información del thumbnail
                $media->extra = array_merge($media->extra ?? [], [
                    'thumbnail_path' => $thumbnailPath
                ]);
                $media->save();

                $data['thumbnail_url'] = Storage::disk($this->disk)->url($thumbnailPath);
                return response()->json($data);

            } catch (\Throwable $e) {
                Log::error('Error creating thumbnail: ' . $e->getMessage());
                return $response; // Devolver respuesta original si falla el thumbnail
            }
        } catch (\Exception $e) {
            Log::error('Error en la subida de imagen', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al procesar la imagen: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($filename)
    {
        $media = Media::where('name', $filename)->first();
        
        if (!$media) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Eliminar el archivo físico y su miniatura
        Storage::disk($this->disk)->delete($media->path);
        if (isset($media->custom_properties['thumbnail_path'])) {
            Storage::disk($this->disk)->delete($media->custom_properties['thumbnail_path']);
        }
        
        // Eliminar el registro de Media
        $media->delete();

        return response()->json(['success' => true]);
    }

    protected function createThumbnail($file, $filename)
    {
        try {
            Storage::disk($this->disk)->makeDirectory($this->directory . '/thumbnails');
            $thumbnailPath = $this->directory . '/thumbnails/' . $filename;
            
            $image = $this->manager->read($file->getRealPath())
                ->scaleDown(width: $this->thumbnailSize)
                ->save(Storage::disk($this->disk)->path($thumbnailPath));

            return $thumbnailPath;
        } catch (\Throwable $e) {
            Log::error('Error creating thumbnail: ' . $e->getMessage());
            throw $e;
        }
    }
}
