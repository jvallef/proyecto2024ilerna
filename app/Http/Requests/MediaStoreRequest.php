<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

class MediaStoreRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $collection = $this->input('collection');
        
        Log::info('MediaStoreRequest::rules', [
            'collection' => $collection,
            'all_input' => $this->all(),
            'files' => $this->allFiles()
        ]);
        
        $config = config("media.{$collection}") ?? config('media');
        
        Log::info('MediaStoreRequest::config', [
            'config' => $config,
            'full_config' => config('media')
        ]);

        return [
            'file' => [
                'required',
                'file',
                'max:' . ($config['max_file_size'] ?? config('media.max_file_size')),
                'mimes:' . implode(',', $config['allowed_types'] ?? config('media.allowed_types')),
                function ($attribute, $value, $fail) use ($config) {
                    $maxDimensions = $config['max_dimensions'] ?? config('media.max_dimensions');
                    
                    if (!$value || !$value->isValid()) {
                        $fail('El archivo no es válido');
                        return;
                    }
                    
                    try {
                        list($width, $height) = getimagesize($value->getRealPath());
                        Log::info('MediaStoreRequest::dimensions', [
                            'width' => $width,
                            'height' => $height,
                            'max' => $maxDimensions
                        ]);
                        
                        if ($width > $maxDimensions || $height > $maxDimensions) {
                            $fail("La imagen es demasiado grande ({$width}x{$height}px). El tamaño máximo permitido es {$maxDimensions}x{$maxDimensions}px.");
                        }
                    } catch (\Exception $e) {
                        Log::error('Error al validar dimensiones', [
                            'error' => $e->getMessage()
                        ]);
                        $fail('No se pudieron validar las dimensiones de la imagen');
                    }
                }
            ],
            'collection' => 'required|string',
            'model_type' => 'required|string',
            'model_id' => 'required'
        ];
    }
}
