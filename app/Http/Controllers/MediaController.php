<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MediaController extends Controller
{
    protected function getConfig($type = 'default')
    {
        return $type === 'avatar' ? config('media.avatar') : config('media');
    }

    protected function getModel($modelType, $modelId)
    {
        $modelClass = '\\App\\Models\\' . $modelType;
        return $modelClass::find($modelId);
    }

    /**
     * Subir un archivo multimedia
     */
    public function store(Request $request)
    {
        try {
            \Log::info('Recibiendo solicitud de subida', [
                'files' => $request->allFiles(),
                'all' => $request->all()
            ]);

            $collection = $request->input('collection', 'default');
            $config = $this->getConfig($collection);

            $validator = Validator::make($request->all(), [
                'file' => [
                    'required',
                    'file',
                    'max:' . $config['max_file_size'],
                    Rule::dimensions()
                        ->maxWidth($config['max_dimensions'])
                        ->maxHeight($config['max_dimensions']),
                    function ($attribute, $value, $fail) use ($config) {
                        if (!$value || !$value->isValid()) {
                            $fail('El archivo no es válido');
                            return;
                        }
                        $extension = strtolower($value->getClientOriginalExtension());
                        if (!in_array($extension, $config['allowed_types'])) {
                            $fail('Tipo de archivo no permitido. Tipos aceptados: ' . implode(', ', $config['allowed_types']));
                        }
                    },
                ],
                'model_type' => 'required|string',
                'model_id' => 'required',
                'collection' => 'required|string',
            ], [
                'file.max' => 'El archivo excede el tamaño máximo permitido de ' . ($config['max_file_size']/1024) . 'MB',
                'file.required' => 'Debe seleccionar un archivo',
                'file.file' => 'El archivo no es válido',
                'file.dimensions' => 'Dimensiones inválidas. Máximo ' . $config['max_dimensions'] . 'x' . $config['max_dimensions'] . ' píxeles'
            ]);

            if ($validator->fails()) {
                \Log::warning('Validación fallida', [
                    'errors' => $validator->errors()->toArray()
                ]);
                return response()->json(['error' => $validator->errors()->first()], 422);
            }

            // Validación adicional de archivo
            $file = $request->file('file');
            if (!$file) {
                return response()->json(['error' => 'No se encontró el archivo'], 422);
            }

            \Log::info('Archivo recibido', [
                'nombre' => $file->getClientOriginalName(),
                'tamaño' => $file->getSize(),
                'tipo' => $file->getMimeType(),
                'extensión' => $file->getClientOriginalExtension()
            ]);

            $model = app($request->input('model_type'))->find($request->input('model_id'));
            if (!$model) {
                return response()->json(['error' => 'Modelo no encontrado'], 404);
            }

            // Verificar permisos
            if (!$this->checkPermissions($model)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $media = $model->addMediaFromRequest('file')
                ->toMediaCollection($collection);

            return response()->json([
                'success' => true,
                'url' => $media->getUrl(),
            ]);

        } catch (\Exception $e) {
            \Log::error('Error en subida de archivo', [
                'mensaje' => $e->getMessage(),
                'línea' => $e->getLine(),
                'archivo' => $e->getFile()
            ]);

            return response()->json([
                'error' => 'Error al procesar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un archivo multimedia
     */
    public function destroy(Media $media)
    {
        try {
            // Verificar permisos
            if (!$this->checkPermissions($media->model)) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $media->delete();

            return response()->json(['success' => true]);

        } catch (\Exception $e) {
            Log::error('Error al eliminar archivo', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Error al eliminar el archivo: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar permisos del usuario actual sobre el modelo
     */
    protected function checkPermissions($model): bool
    {
        $user = Auth::user();
        
        // Si el usuario es admin, tiene todos los permisos
        if ($user->hasRole('admin')) {
            return true;
        }

        // Si el modelo es el propio usuario
        if ($model instanceof \App\Models\User && $model->id === $user->id) {
            return true;
        }

        // Aquí puedes añadir más reglas de permisos según tus necesidades
        
        return false;
    }
}
