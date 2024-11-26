<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\User;
use App\Http\Requests\MediaStoreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
    protected $disk = 'public';
    protected $directory = 'uploads';

    public function store(MediaStoreRequest $request)
    {
        try {
            $file = $request->file('file');
            $filename = $this->generateFilename($file);
            $path = $file->storeAs($this->directory, $filename, $this->disk);

            $media = Media::create([
                'name' => $filename,
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'path' => $path,
                'disk' => $this->disk,
                'file_hash' => hash_file('md5', $file->getRealPath()),
                'collection' => $request->input('collection'),
                'size' => $file->getSize(),
                'mediable_type' => $request->input('model_type'),
                'mediable_id' => $request->input('model_id')
            ]);

            return response()->json([
                'success' => true,
                'media' => $media,
                'url' => Storage::disk($this->disk)->url($path)
            ]);
        } catch (\Exception $e) {
            Log::error('Error en FileController::store', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'errors' => [
                    'file' => [$e->getMessage()]
                ]
            ], 422);
        }
    }

    public function destroy($filename)
    {
        $media = Media::where('name', $filename)->first();
        
        if (!$media) {
            return response()->json(['error' => 'File not found'], 404);
        }

        // Eliminar el archivo físico
        Storage::disk($this->disk)->delete($media->path);
        
        // Eliminar el registro de Media
        $media->delete();

        return response()->json(['success' => true]);
    }

    protected function generateFilename($file)
    {
        $extension = $file->getClientOriginalExtension();
        return Str::uuid() . '.' . $extension;
    }
}
